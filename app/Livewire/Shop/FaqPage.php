<?php

namespace App\Livewire\Shop;

use App\Models\CmsPage;
use Livewire\Component;

class FaqPage extends Component
{
    public function render()
    {
        $faqs = CmsPage::query()
            ->where('type', 'faq')
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->get();

        return view('livewire.shop.faq-page', [
            'faqs' => $faqs,
        ])->layout('layouts.shop', ['title' => 'Bantuan & FAQ']);
    }
}
