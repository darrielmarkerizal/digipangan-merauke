<?php

namespace App\Http\Controllers;

use App\Support\PublicUrl;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Modules\Farmer\Repositories\Contracts\FarmerRepositoryInterface;
use Modules\Post\Repositories\Contracts\PostRepositoryInterface;
use Modules\Product\Repositories\Contracts\ProductRepositoryInterface;
use Modules\Region\Repositories\Contracts\RegionRepositoryInterface;

class SitemapController extends Controller
{
    public function __construct(
        private readonly ProductRepositoryInterface $products,
        private readonly RegionRepositoryInterface $regions,
        private readonly FarmerRepositoryInterface $farmers,
        private readonly PostRepositoryInterface $posts,
    ) {}

    public function index(): Response
    {
        $urls = collect([
            ['loc' => PublicUrl::home()],
            ['loc' => PublicUrl::products()],
            ['loc' => PublicUrl::posts()],
            ['loc' => PublicUrl::about()],
        ])
            ->merge($this->products->publicSitemapEntries()
                ->map(fn ($p) => ['loc' => PublicUrl::product($p->slug), 'lastmod' => $p->updated_at]))
            ->merge($this->regions->publicSitemapEntries()
                ->map(fn ($r) => ['loc' => PublicUrl::region($r->slug), 'lastmod' => $r->updated_at]))
            ->merge($this->farmers->publicSitemapEntries()
                ->map(fn ($f) => ['loc' => PublicUrl::farmer($f->slug), 'lastmod' => $f->updated_at]))
            ->merge($this->posts->publicSitemapEntries()
                ->map(fn ($p) => ['loc' => PublicUrl::post($p->slug), 'lastmod' => $p->updated_at]))
            ->all();

        return response($this->render($urls), 200)
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    private function render(array $urls): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        foreach ($urls as $url) {
            $xml .= '  <url>'."\n";
            $xml .= '    <loc>'.htmlspecialchars($url['loc'], ENT_XML1 | ENT_QUOTES).'</loc>'."\n";

            if (! empty($url['lastmod']) && $url['lastmod'] instanceof Carbon) {
                $xml .= '    <lastmod>'.$url['lastmod']->toAtomString().'</lastmod>'."\n";
            }

            $xml .= '  </url>'."\n";
        }

        return $xml.'</urlset>'."\n";
    }
}
