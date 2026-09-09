<?php

use App\Models\CmsPage;
use Database\Seeders\CmsContentSeeder;

it('seeds full footer cms pages and faq content', function () {
    $this->seed(CmsContentSeeder::class);

    foreach (['kebijakan-privasi', 'syarat-ketentuan', 'kebijakan-retur', 'cara-belanja', 'tentang-kami', 'retur'] as $slug) {
        $page = CmsPage::query()->where('slug', $slug)->where('is_published', true)->first();
        expect($page)->not->toBeNull()
            ->and(strlen(strip_tags((string) $page->body)))->toBeGreaterThan(120);
    }

    expect(CmsPage::query()->where('type', 'faq')->where('is_published', true)->count())->toBeGreaterThanOrEqual(8);

    $this->get(route('cms.show', 'kebijakan-privasi'))->assertOk()->assertSee('Data yang Kami Kumpulkan');
    $this->get(route('cms.show', 'syarat-ketentuan'))->assertOk()->assertSee('Akun &amp; Pemesanan', false);
    $this->get(route('cms.show', 'kebijakan-retur'))->assertOk()->assertSee('Periode Pengajuan');
    $this->get(route('faq'))->assertOk()->assertSee('Berapa minimum order?');
});
