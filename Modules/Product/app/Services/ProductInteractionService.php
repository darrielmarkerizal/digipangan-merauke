<?php

namespace Modules\Product\Services;

use Illuminate\Http\Request;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductInteraction;

class ProductInteractionService
{
    /**
     * Capture request context synchronously, then record the interaction after
     * the response is sent so page latency is unaffected. Recording failures are
     * swallowed (reported) so they can never break the visitor-facing request.
     */
    public function recordDeferred(Product $product, string $type, Request $request): void
    {
        $productId = $product->id;
        $regionId = $product->region_id;
        $ip = $request->ip();
        $userAgent = (string) $request->userAgent();
        $referrer = $request->headers->get('referer');

        dispatch(function () use ($productId, $regionId, $type, $ip, $userAgent, $referrer) {
            try {
                app(self::class)->record($productId, $regionId, $type, $ip, $userAgent, $referrer);
            } catch (\Throwable $e) {
                report($e);
            }
        })->afterResponse();
    }

    /**
     * Persist a single interaction. `view` events are deduplicated per visitor
     * per day; `contact` events are always recorded (each click is a signal).
     */
    public function record(int $productId, int $regionId, string $type, ?string $ip, ?string $userAgent, ?string $referrer): void
    {
        $visitorHash = $this->visitorHash($ip, $userAgent);

        if ($type === ProductInteraction::TYPE_VIEW && $this->alreadyViewedToday($productId, $visitorHash)) {
            return;
        }

        ProductInteraction::create([
            'product_id' => $productId,
            'region_id' => $regionId,
            'type' => $type,
            'visitor_hash' => $visitorHash,
            'referrer_host' => $this->referrerHost($referrer),
            'occurred_at' => now(),
        ]);
    }

    private function alreadyViewedToday(int $productId, string $visitorHash): bool
    {
        return ProductInteraction::query()
            ->where('product_id', $productId)
            ->where('type', ProductInteraction::TYPE_VIEW)
            ->where('visitor_hash', $visitorHash)
            ->whereDate('occurred_at', now()->toDateString())
            ->exists();
    }

    /**
     * SHA-256 of ip + user agent + a daily-rotated salt. Not reversible to an
     * identity and not correlatable across days (the salt changes each day).
     */
    private function visitorHash(?string $ip, ?string $userAgent): string
    {
        $dailySalt = hash('sha256', (string) config('app.key').'|'.now()->toDateString());

        return hash('sha256', ($ip ?? '').'|'.($userAgent ?? '').'|'.$dailySalt);
    }

    private function referrerHost(?string $referrer): ?string
    {
        if (! $referrer) {
            return null;
        }

        $host = parse_url($referrer, PHP_URL_HOST);

        return $host ? mb_substr($host, 0, 100) : null;
    }
}
