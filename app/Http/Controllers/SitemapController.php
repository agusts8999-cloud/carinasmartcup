<?php

namespace App\Http\Controllers;

use App\Enums\ProductStatus;
use App\Models\CmsPage;
use App\Models\Product;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = collect([
            ['loc' => url('/'), 'priority' => '1.0'],
            ['loc' => route('catalog'), 'priority' => '0.9'],
            ['loc' => route('faq'), 'priority' => '0.5'],
            ['loc' => route('shipping.check'), 'priority' => '0.5'],
            ['loc' => route('order.track'), 'priority' => '0.4'],
        ]);

        Product::query()
            ->where('status', ProductStatus::Published)
            ->orderBy('id')
            ->each(function (Product $product) use ($urls): void {
                $urls->push([
                    'loc' => route('product.show', $product->slug),
                    'priority' => '0.8',
                    'lastmod' => optional($product->updated_at)?->toAtomString(),
                ]);
            });

        CmsPage::query()
            ->where('is_published', true)
            ->where('type', 'page')
            ->each(function (CmsPage $page) use ($urls): void {
                $urls->push([
                    'loc' => route('cms.show', $page->slug),
                    'priority' => '0.4',
                ]);
            });

        $xml = view('sitemap', ['urls' => $urls])->render();

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }
}
