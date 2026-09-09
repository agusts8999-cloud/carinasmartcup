<?php

namespace App\Livewire\Shop;

use App\Models\CmsPage;
use Livewire\Component;

class CmsPageShow extends Component
{
    public CmsPage $page;

    public function mount(string $slug): void
    {
        $this->page = CmsPage::query()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();
    }

    public function render()
    {
        return view('livewire.shop.cms-page-show')
            ->layout('layouts.shop', ['title' => $this->page->title]);
    }
}
