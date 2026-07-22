<?php

namespace App\Http\Controllers;

use App\Support\PublicUrl;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Modules\Farmer\Models\Farmer;
use Modules\Post\Models\Post;
use Modules\Product\Models\Product;
use Modules\Region\Models\Region;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $urls = collect([
            ['loc' => PublicUrl::home()],
            ['loc' => PublicUrl::products()],
            ['loc' => PublicUrl::posts()],
            ['loc' => PublicUrl::about()],
        ])
            ->merge(Product::where('is_active', true)->get(['slug', 'updated_at'])
                ->map(fn (Product $p) => ['loc' => PublicUrl::product($p->slug), 'lastmod' => $p->updated_at]))
            ->merge(Region::where('is_active', true)->get(['slug', 'updated_at'])
                ->map(fn (Region $r) => ['loc' => PublicUrl::region($r->slug), 'lastmod' => $r->updated_at]))
            ->merge(Farmer::where('is_active', true)->get(['slug', 'updated_at'])
                ->map(fn (Farmer $f) => ['loc' => PublicUrl::farmer($f->slug), 'lastmod' => $f->updated_at]))
            ->merge(Post::published()->get(['slug', 'updated_at'])
                ->map(fn (Post $p) => ['loc' => PublicUrl::post($p->slug), 'lastmod' => $p->updated_at]))
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
