<?php

use App\Models\User;
use App\Support\DashboardMetrics;
use Database\Seeders\RoleSeeder;

it('returns seven day metric series', function () {
    $series = app(DashboardMetrics::class)->lastSevenDays();

    expect($series['labels'])->toHaveCount(7)
        ->and($series['orders'])->toHaveCount(7)
        ->and($series['revenue_millions'])->toHaveCount(7)
        ->and($series['pending_payments'])->toHaveCount(7);

    $kpi = app(DashboardMetrics::class)->kpi();
    expect($kpi)->toHaveKeys(['orders_today', 'pending_payments', 'revenue_month', 'low_stock']);
});

it('renders enterprise dashboard for admin', function () {
    $this->seed(RoleSeeder::class);

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin)
        ->get('/admin')
        ->assertOk()
        ->assertSee('Ringkasan Operasional', false)
        ->assertSee('Command Center', false)
        ->assertSee('Tren 7 Hari', false)
        ->assertSee('Antrian Perhatian', false);
});
