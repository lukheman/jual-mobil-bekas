<?php

use App\Models\PageView;
use App\Models\User;

test('public page visits are tracked', function () {
    $this->get('/')->assertOk();
    $this->get('/tentang-kami')->assertOk();

    expect(PageView::query()->count())->toBe(2)
        ->and(PageView::query()->where('route_name', 'home')->exists())->toBeTrue()
        ->and(PageView::query()->where('route_name', 'about')->exists())->toBeTrue();
});

test('admin pages are not tracked as visitor views', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/admin/dashboard')->assertOk();

    expect(PageView::query()->count())->toBe(0);
});

test('the landing page displays public visitor statistics', function () {
    PageView::create([
        'path' => '/',
        'route_name' => 'home',
        'visitor_hash' => hash('sha256', 'visitor-one'),
        'ip_hash' => hash('sha256', '127.0.0.1'),
        'created_at' => now(),
    ]);
    PageView::create([
        'path' => '/tentang-kami',
        'route_name' => 'about',
        'visitor_hash' => hash('sha256', 'visitor-two'),
        'ip_hash' => hash('sha256', '127.0.0.2'),
        'created_at' => now(),
    ]);

    $this->get('/')
        ->assertOk()
        ->assertSee('id="statistik"', false)
        ->assertSee('STATISTIK PENGUNJUNG')
        ->assertSee('Total kunjungan')
        ->assertSee('Pengunjung unik')
        ->assertSee('Kunjungan hari ini')
        ->assertSee('Kunjungan 7 hari terakhir');
});

test('the dashboard displays visitor statistics', function () {
    $user = User::factory()->create();

    PageView::create([
        'path' => '/',
        'route_name' => 'home',
        'visitor_hash' => hash('sha256', 'visitor-one'),
        'ip_hash' => hash('sha256', '127.0.0.1'),
        'created_at' => now(),
    ]);
    PageView::create([
        'path' => '/jual-mobil',
        'route_name' => 'sell-car',
        'visitor_hash' => hash('sha256', 'visitor-two'),
        'ip_hash' => hash('sha256', '127.0.0.2'),
        'created_at' => now(),
    ]);

    $this->actingAs($user)->get('/admin/dashboard')
        ->assertOk()
        ->assertSee('Statistik Pengunjung')
        ->assertSee('Halaman Terpopuler')
        ->assertSee('Total kunjungan')
        ->assertSee('Pengunjung unik')
        ->assertSee('Beranda')
        ->assertSee('Jual Mobil');
});
