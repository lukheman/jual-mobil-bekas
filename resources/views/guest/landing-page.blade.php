@php
    $whatsapp = $settings['whatsapp_number'] ?? '6281234567890';
    $phone = $settings['phone_number'] ?? '081234567890';
    $address = $settings['address'] ?? 'Jl. Ahmad Yani No. 88, Samarinda';
    $siteName = $settings['site_name'] ?? 'RodaTa';
    $socialLinks = collect([
        ['key' => 'facebook_url', 'icon' => 'fab fa-facebook-f', 'label' => 'Facebook'],
        ['key' => 'instagram_url', 'icon' => 'fab fa-instagram', 'label' => 'Instagram'],
        ['key' => 'tiktok_url', 'icon' => 'fab fa-tiktok', 'label' => 'TikTok'],
        ['key' => 'youtube_url', 'icon' => 'fab fa-youtube', 'label' => 'YouTube'],
        ['key' => 'twitter_url', 'icon' => 'fab fa-x-twitter', 'label' => 'X / Twitter'],
    ])->filter(fn (array $social) => filled($settings[$social['key']] ?? null));
    $testimonials = [
        [
            'name' => 'Andi Pratama',
            'initials' => 'AP',
            'role' => 'Pembeli Toyota Avanza',
            'quote' => 'Prosesnya cepat dan transparan. Kondisi mobil sesuai foto, tim juga sangat membantu dari awal sampai serah terima.',
        ],
        [
            'name' => 'Siti Rahma',
            'initials' => 'SR',
            'role' => 'Pembeli Honda Brio',
            'quote' => 'Saya dibantu memilih mobil sesuai kebutuhan dan budget. Tidak merasa ditekan, semuanya dijelaskan dengan detail.',
        ],
        [
            'name' => 'Budi Santoso',
            'initials' => 'BS',
            'role' => 'Pembeli Mitsubishi Xpander',
            'quote' => 'Kualitas unit memuaskan dan proses pembelian sangat mudah. Sekarang keluarga saya nyaman bepergian.',
        ],
    ];
    $imageFor = static function ($car): string {
        $brand = strtolower($car->carModel?->brand?->name ?? '');
        $model = strtolower($car->carModel?->name ?? '');
        return match (true) {
            str_contains($model, 'avanza') => asset('assets/images/cars/toyota-avanza.webp'),
            str_contains($model, 'hr-v') || str_contains($model, 'hrv') => asset('assets/images/cars/honda-hrv.webp'),
            str_contains($model, 'xpander') => asset('assets/images/cars/mitsubishi-xpander.webp'),
            str_contains($model, 'innova') => asset('assets/images/cars/toyota-innova-zenix.webp'),
            str_contains($model, 'brio') => asset('assets/images/cars/honda-brio.webp'),
            str_contains($model, 'fortuner') => asset('assets/images/cars/toyota-fortuner.webp'),
            str_contains($model, 'ertiga') => asset('assets/images/cars/suzuki-ertiga.webp'),
            str_contains($model, 'br-v') || str_contains($model, 'brv') => asset('assets/images/cars/honda-brv.webp'),
            default => $car->primaryPhoto?->path ? asset('storage/'.$car->primaryPhoto->path) : asset('assets/images/cars/toyota-avanza.webp'),
        };
    };
    $nameFor = static fn ($car): string => trim(($car->carModel?->brand?->name ?? '').' '.($car->carModel?->name ?? '').' '.($car->variant ?? ''));
    $whatsappUrl = static fn (?string $message = null): string => 'https://wa.me/'.$whatsapp.'?text='.rawurlencode($message ?? 'Halo MobilTa, saya ingin konsultasi mobil bekas.');
    $formatPrice = static fn (int $price): string => 'Rp '.number_format($price / 1_000_000, 0, ',', '.').' jt';
@endphp

<script>
    if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        document.documentElement.classList.add('rk-motion');
    }
</script>

<x-layouts.guest :title="$siteName.' — Mobil Bekas Terpercaya'" :description="'Temukan mobil bekas berkualitas dengan harga transparan dan proses mudah di '.$siteName.'.'" :settings="$settings">
    <div class="rk-progress" aria-hidden="true"><span></span></div>

    <section class="rk-hero">
        <div class="rk-hero-image"></div>
        <div class="rk-hero-overlay"></div>
        <div class="rk-container rk-hero-content">
            <div class="rk-eyebrow"><span></span> SHOWROOM MOBIL BEKAS TERPERCAYA</div>
            <h1>Temukan mobil yang<br>tepat untukmu.</h1>
            <p>Mobil berkualitas, harga transparan, dan proses yang<br class="desktop-only"> dibuat sesederhana mungkin.</p>
            <div class="rk-hero-actions">
                <a href="#stok" class="rk-button rk-button-green">Lihat Stok Mobil <i class="fas fa-arrow-right"></i></a>
                <a href="{{ $whatsappUrl() }}" target="_blank" class="rk-button rk-button-light"><i class="fab fa-whatsapp"></i> Konsultasi Gratis</a>
            </div>
            <div class="rk-trust-row">
                <div><strong>10+</strong><span>Tahun pengalaman</span></div>
                <div><strong>500+</strong><span>Unit terjual</span></div>
                <div><strong>4.9/5</strong><span>Rating pelanggan</span></div>
            </div>
        </div>
        <a href="#stok" class="rk-scroll"><span>Scroll untuk menjelajah</span><i class="fas fa-arrow-down"></i></a>
    </section>

    <section class="rk-search-wrap" id="cari">
        <div class="rk-container">
            <form class="rk-search-card" action="{{ route('home') }}#stok" method="GET">
                <div class="rk-search-title"><span class="rk-search-icon"><i class="fas fa-sliders"></i></span><div><strong>Cari mobil impianmu</strong><small>Pilih sesuai kebutuhan dan budget</small></div></div>
                <label><span>Merek</span><select name="brand"><option value="">Semua merek</option>@foreach($brands as $brand)<option value="{{ $brand->slug }}">{{ $brand->name }}</option>@endforeach</select></label>
                <label><span>Tipe bodi</span><select name="body_type"><option value="">Semua tipe</option>@foreach($bodyTypes as $type)<option value="{{ $type->value }}">{{ $type->label() }}</option>@endforeach</select></label>
                <label><span>Budget maksimal</span><select name="max_price"><option value="">Semua harga</option><option value="150000000">Rp 150 juta</option><option value="250000000">Rp 250 juta</option><option value="400000000">Rp 400 juta</option></select></label>
                <button class="rk-search-submit" type="submit"><i class="fas fa-search"></i> Cari Mobil</button>
            </form>
        </div>
    </section>

    <section class="rk-ticker" aria-hidden="true">
        <div class="rk-ticker-track">
            @for ($copy = 0; $copy < 6; $copy++)
                <div class="rk-ticker-group">
                    <span>Inspeksi menyeluruh</span><i></i>
                    <span>Harga transparan</span><i></i>
                    <span>Garansi pilihan</span><i></i>
                    <span>Test drive</span><i></i>
                    <span>Dokumen dibantu</span><i></i>
                </div>
            @endfor
        </div>
    </section>

    <section class="rk-section rk-featured" id="stok">
        <div class="rk-container">
            <div class="rk-section-head"><div><div class="rk-kicker">PILIHAN TERBAIK</div><h2>Unit unggulan minggu ini</h2></div><a href="#semua-stok" class="rk-text-link">Lihat semua stok <i class="fas fa-arrow-right"></i></a></div>
            <div class="rk-car-grid rk-car-grid-featured">
                @forelse($featuredCars as $car)
                    <article class="rk-car-card"><a href="{{ route('cars.show', $car) }}" class="rk-car-photo"><img src="{{ $imageFor($car) }}" alt="{{ $nameFor($car) }}" loading="lazy"><span class="rk-status">Tersedia</span><span class="rk-heart"><i class="far fa-heart"></i></span></a><div class="rk-car-body"><div class="rk-car-meta">{{ $car->year }} <i></i> {{ number_format($car->mileage / 1000, 0, ',', '.') }} rb km <i></i> {{ $car->transmission->label() }}</div><h3>{{ $nameFor($car) }}</h3><strong class="rk-price">{{ $formatPrice($car->price) }}</strong><a href="{{ $whatsappUrl('Halo MobilTa, saya tertarik dengan '.$nameFor($car).'.') }}" target="_blank" class="rk-wa-link"><i class="fab fa-whatsapp"></i> Tanya via WhatsApp</a></div></article>
                @empty
                    <article class="rk-empty"><i class="fas fa-car"></i><h3>Stok unggulan segera hadir</h3><p>Hubungi kami untuk mendapatkan rekomendasi unit terbaru.</p></article>
                @endforelse
            </div>
        </div>
    </section>

    <section class="rk-section rk-light" id="keunggulan">
        <div class="rk-container">
            <div class="rk-section-head"><div><div class="rk-kicker">KENAPA {{ $siteName }}</div><h2>Beli mobil bekas<br>tanpa rasa khawatir.</h2></div><p class="rk-head-copy">Kami percaya membeli mobil bekas harusnya terasa menyenangkan. Setiap unit kami pilih dan periksa dengan standar yang sama.</p></div>
            <div class="rk-benefits"><div class="rk-benefit"><span>01</span><i class="fas fa-clipboard-check"></i><h3>Sudah melalui inspeksi</h3><p>Setiap mobil melewati pemeriksaan menyeluruh sebelum tampil di showroom.</p></div><div class="rk-benefit"><span>02</span><i class="fas fa-receipt"></i><h3>Harga transparan</h3><p>Tidak ada biaya tersembunyi. Harga yang kamu lihat adalah harga terbaik.</p></div><div class="rk-benefit"><span>03</span><i class="fas fa-handshake"></i><h3>Proses mudah</h3><p>Dibantu dari konsultasi, test drive, hingga mobil sampai di rumah.</p></div><div class="rk-benefit"><span>04</span><i class="fas fa-shield-alt"></i><h3>Garansi pilihan</h3><p>Rasa tenang setelah transaksi dengan opsi garansi untuk unit tertentu.</p></div></div>
        </div>
    </section>

    <section class="rk-section rk-inventory" id="semua-stok">
        <div class="rk-container"><div class="rk-section-head"><div><div class="rk-kicker">STOK TERBARU</div><h2>Siap dibawa pulang.</h2></div><a href="#cari" class="rk-filter-link"><i class="fas fa-sliders"></i> Filter stok</a></div><div class="rk-car-grid rk-car-grid-inventory">
            @forelse($latestCars as $car)
                <article class="rk-car-card"><a href="{{ route('cars.show', $car) }}" class="rk-car-photo"><img src="{{ $imageFor($car) }}" alt="{{ $nameFor($car) }}" loading="lazy"><span class="rk-status">Tersedia</span></a><div class="rk-car-body"><div class="rk-car-meta">{{ $car->year }} <i></i> {{ number_format($car->mileage / 1000, 0, ',', '.') }} rb km <i></i> {{ $car->transmission->label() }}</div><h3>{{ $nameFor($car) }}</h3><strong class="rk-price">{{ $formatPrice($car->price) }}</strong><a href="{{ $whatsappUrl('Halo MobilTa, saya tertarik dengan '.$nameFor($car).'.') }}" target="_blank" class="rk-wa-link"><i class="fab fa-whatsapp"></i> Tanya via WhatsApp</a></div></article>
            @empty
                <div class="rk-empty rk-empty-wide"><i class="fas fa-car"></i><h3>Belum ada stok aktif</h3><p>Admin bisa menambahkan unit dari panel admin.</p></div>
            @endforelse
        </div></div>
    </section>

    <section class="rk-testimonials" id="testimoni">
        <div class="rk-container">
            <div class="rk-section-head">
                <div>
                    <div class="rk-kicker">CERITA PELANGGAN</div>
                    <h2>Mereka sudah<br>menemukan mobilnya.</h2>
                </div>
                <div class="rk-rating"><strong>4.9</strong><span><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></span><small>Rating pelanggan</small></div>
            </div>
            <div class="rk-testimonial-grid">
                @foreach ($testimonials as $testimonial)
                    <article class="rk-testimonial-card">
                        <div class="rk-testimonial-stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                        <blockquote>“{{ $testimonial['quote'] }}”</blockquote>
                        <div class="rk-testimonial-author">
                            <span class="rk-testimonial-avatar">{{ $testimonial['initials'] }}</span>
                            <span><strong>{{ $testimonial['name'] }}</strong><small>{{ $testimonial['role'] }}</small></span>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

     <section class="rk-process" id="proses"><div class="rk-container rk-process-layout"><div><div class="rk-kicker">CARA BELI DI {{ $siteName }}</div><h2>Semudah<br>mengobrol.</h2><p>Mulai dari cerita kebutuhanmu. Kami bantu cari, pilihkan, dan siapkan mobil yang paling cocok.</p><a href="{{ $whatsappUrl() }}" target="_blank" class="rk-button rk-button-green">Mulai Konsultasi <i class="fas fa-arrow-right"></i></a></div><div class="rk-steps"><div><b>01</b><span><strong>Ceritakan kebutuhanmu</strong><small>Budget, kebutuhan, dan preferensi. Kami dengarkan.</small></span></div><div><b>02</b><span><strong>Pilih unit yang cocok</strong><small>Kami kirimkan detail, foto, dan video unit pilihanmu.</small></span></div><div><b>03</b><span><strong>Test drive & inspeksi</strong><small>Lihat dan rasakan langsung unit pilihanmu.</small></span></div><div><b>04</b><span><strong>Mobil siap di jalan</strong><small>Urusan dokumen kami bantu sampai selesai.</small></span></div></div></div></section>

     <section class="rk-cta" id="kontak"><div class="rk-container rk-cta-inner"><div><div class="rk-kicker">PUNYA PERTANYAAN?</div><h2>Mobil impianmu<br>mungkin sudah menunggu.</h2></div><div><p>Chat kami sekarang. Konsultasi gratis, tanpa kewajiban membeli.</p><a href="{{ $whatsappUrl() }}" target="_blank" class="rk-button rk-button-white"><i class="fab fa-whatsapp"></i> Chat WhatsApp</a><a href="tel:{{ $phone }}" class="rk-phone"><i class="fas fa-phone"></i> {{ $phone }}</a>@if($socialLinks->isNotEmpty())<div class="rk-social-links"><span>Ikuti kami</span><div>@foreach($socialLinks as $social)<a href="{{ $settings[$social['key']] }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $social['label'] }}"><i class="{{ $social['icon'] }}"></i></a>@endforeach</div></div>@endif</div></div></section>
</x-layouts.guest>

<style>
    :root{--rk-ink:#17352b;--rk-green:#1d5b42;--rk-lime:#d8ed68;--rk-muted:#71817a;--rk-line:#dce6e1;--rk-paper:#f5f7f5}.rk-container{max-width:1160px;margin:auto;padding:0 28px}.rk-hero{height:760px;position:relative;background:#112c24;color:#fff;overflow:hidden}.rk-hero-image{position:absolute;inset:0;background-image:url('{{ asset('assets/images/cars/hero-showroom.webp') }}');background-size:cover;background-position:center;filter:saturate(.78)}.rk-hero-overlay{position:absolute;inset:0;z-index:0;background:linear-gradient(90deg,rgba(10,35,27,.9),rgba(14,39,31,.43) 55%,rgba(14,39,31,.08))}.rk-hero-content{position:relative;z-index:2;height:100%;display:flex;flex-direction:column;justify-content:center;padding-top:60px}.rk-eyebrow,.rk-kicker{font-size:11px;font-weight:700;letter-spacing:2.2px;color:var(--rk-lime)}.rk-eyebrow{display:flex;align-items:center;gap:10px;margin-bottom:25px}.rk-eyebrow span{display:block;width:28px;height:1px;background:var(--rk-lime)}.rk-hero h1{font-family:'Playfair Display',serif;font-size:72px;line-height:1.05;letter-spacing:-2px;margin:0 0 26px;font-weight:700}.rk-hero p{font-size:17px;line-height:1.7;color:rgba(255,255,255,.75);margin:0 0 32px}.rk-hero-actions{display:flex;gap:12px}.rk-button{display:inline-flex;align-items:center;justify-content:center;gap:12px;padding:14px 21px;border-radius:3px;text-decoration:none;font-size:13px;font-weight:700;transition:.25s}.rk-button:hover{transform:translateY(-2px)}.rk-button-green{color:#17352b;background:var(--rk-lime)}.rk-button-light{color:#fff;border:1px solid rgba(255,255,255,.4);background:rgba(255,255,255,.08)}.rk-button-white{color:var(--rk-green);background:#fff}.rk-trust-row{display:flex;gap:40px;margin-top:70px}.rk-trust-row div{display:flex;flex-direction:column;gap:3px;border-left:1px solid rgba(255,255,255,.3);padding-left:14px}.rk-trust-row strong{font-family:'Playfair Display',serif;font-size:25px}.rk-trust-row span{font-size:11px;color:rgba(255,255,255,.6)}.rk-scroll{position:absolute;z-index:2;right:36px;bottom:30px;display:flex;gap:12px;align-items:center;color:rgba(255,255,255,.7);font-size:10px;letter-spacing:1px;text-decoration:none;writing-mode:vertical-rl}.rk-scroll i{color:var(--rk-lime);font-size:14px}.rk-search-wrap{position:relative;margin-top:-58px;z-index:5}.rk-search-card{background:#fff;box-shadow:0 14px 40px rgba(23,53,43,.13);padding:20px 23px;display:grid;grid-template-columns:1.6fr 1fr 1fr 1fr auto;align-items:end;gap:22px}.rk-search-title{display:flex;align-items:center;gap:12px}.rk-search-title strong,.rk-search-title small{display:block}.rk-search-title strong{font-size:14px}.rk-search-title small{font-size:11px;color:var(--rk-muted);margin-top:3px}.rk-search-icon{width:40px;height:40px;display:grid;place-items:center;background:#eaf3ee;color:var(--rk-green);font-size:14px}.rk-search-card label{display:flex;flex-direction:column;gap:7px}.rk-search-card label span{font-size:10px;color:var(--rk-muted);text-transform:uppercase;letter-spacing:1px}.rk-search-card select{border:0;border-bottom:1px solid var(--rk-line);padding:4px 0 9px;background:#fff;color:var(--rk-ink);font-size:13px;outline:0}.rk-search-submit{height:42px;border:0;background:var(--rk-green);color:#fff;padding:0 18px;font-size:12px;font-weight:700;white-space:nowrap}.rk-section{padding:112px 0}.rk-featured{padding-bottom:120px}.rk-section-head{display:flex;justify-content:space-between;align-items:end;margin-bottom:35px}.rk-section h2,.rk-process h2,.rk-cta h2{font-family:'Playfair Display',serif;color:var(--rk-ink);font-size:42px;line-height:1.15;margin:10px 0 0;letter-spacing:-.8px}.rk-text-link,.rk-filter-link{color:var(--rk-green);font-size:12px;font-weight:700;text-decoration:none;padding-bottom:6px;border-bottom:1px solid var(--rk-green)}.rk-text-link i{margin-left:12px}.rk-car-grid{display:grid;gap:20px}.rk-car-grid-featured{grid-template-columns:repeat(4,1fr)}.rk-car-grid-inventory{grid-template-columns:repeat(4,1fr)}.rk-car-card{background:#fff;border:1px solid var(--rk-line);transition:.25s}.rk-car-card:hover{box-shadow:0 14px 30px rgba(23,53,43,.12);transform:translateY(-4px)}.rk-car-photo{height:195px;display:block;position:relative;overflow:hidden;background:#e6ebe8}.rk-car-photo img{width:100%;height:100%;object-fit:cover;transition:.4s}.rk-car-card:hover .rk-car-photo img{transform:scale(1.04)}.rk-status{position:absolute;top:12px;left:12px;background:var(--rk-lime);color:#17352b;font-size:10px;font-weight:700;padding:5px 9px}.rk-heart{position:absolute;right:12px;top:11px;background:#fff;color:var(--rk-green);width:28px;height:28px;display:grid;place-items:center;font-size:12px}.rk-car-body{padding:17px 18px 18px}.rk-car-meta{color:var(--rk-muted);font-size:10px}.rk-car-meta i{display:inline-block;width:3px;height:3px;background:#b5c0ba;border-radius:50%;vertical-align:middle;margin:0 5px}.rk-car-body h3{font-size:15px;margin:10px 0 13px;color:var(--rk-ink);font-weight:700;min-height:18px}.rk-price{font-size:18px;color:var(--rk-green);display:block}.rk-wa-link{display:block;border-top:1px solid var(--rk-line);margin-top:15px;padding-top:13px;color:var(--rk-green);font-size:11px;text-decoration:none;font-weight:700}.rk-wa-link i{font-size:14px;margin-right:5px}.rk-light{background:var(--rk-paper)}.rk-head-copy{max-width:350px;font-size:13px;line-height:1.7;color:var(--rk-muted);margin:0}.rk-benefits{display:grid;grid-template-columns:repeat(4,1fr);gap:20px}.rk-benefit{border-top:1px solid #b8c9c0;padding-top:19px;position:relative}.rk-benefit span{font-size:10px;color:var(--rk-green)}.rk-benefit i{font-size:25px;color:var(--rk-green);display:block;margin:35px 0 20px}.rk-benefit h3{font-size:15px;color:var(--rk-ink);margin-bottom:10px}.rk-benefit p{font-size:12px;color:var(--rk-muted);line-height:1.65;margin:0}.rk-inventory{background:#fff}.rk-filter-link{border:0;padding:10px 13px;background:#eef4f0}.rk-process{background:var(--rk-green);color:#fff;padding:100px 0}.rk-process h2{color:#fff}.rk-process p{color:rgba(255,255,255,.65);font-size:13px;line-height:1.7;max-width:310px;margin:24px 0}.rk-process-layout{display:grid;grid-template-columns:1fr 1fr;gap:120px;align-items:center}.rk-steps{border-top:1px solid rgba(255,255,255,.22)}.rk-steps>div{display:flex;gap:22px;padding:20px 0;border-bottom:1px solid rgba(255,255,255,.22)}.rk-steps b{font-family:'Playfair Display',serif;color:var(--rk-lime);font-size:20px;font-weight:400}.rk-steps span{display:flex;flex-direction:column;gap:5px}.rk-steps strong{font-size:14px}.rk-steps small{font-size:11px;color:rgba(255,255,255,.55)}.rk-cta{background:#bad45b;padding:70px 0}.rk-cta-inner{display:flex;justify-content:space-between;align-items:center}.rk-cta h2{font-size:38px}.rk-cta .rk-kicker{color:#376348}.rk-cta p{font-size:13px;max-width:280px;color:#426342;margin:0 0 18px;line-height:1.6}.rk-phone{display:block;margin-top:15px;color:#376348;font-size:12px;text-decoration:none}.rk-phone i{margin-right:5px}.rk-empty{text-align:center;grid-column:1/-1;padding:70px 20px;background:var(--rk-paper);color:var(--rk-muted)}.rk-empty i{font-size:28px;color:var(--rk-green)}.rk-empty h3{font-size:17px;color:var(--rk-ink);margin:15px 0 5px}.rk-empty p{font-size:12px}.rk-empty-wide{min-height:220px}
    .rk-social-links{margin-top:22px}.rk-social-links>span{display:block;color:var(--rk-green);font-size:10px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;margin-bottom:9px}.rk-social-links>div{display:flex;gap:7px}.rk-social-links a{width:31px;height:31px;border:1px solid rgba(29,91,66,.35);border-radius:50%;display:grid;place-items:center;color:var(--rk-green);text-decoration:none;transition:.2s}.rk-social-links a:hover{background:var(--rk-green);color:#fff;transform:translateY(-2px)}
    .rk-testimonials{padding:115px 0;background:var(--rk-paper)}.rk-rating{display:flex;align-items:center;gap:10px;color:var(--rk-green)}.rk-rating strong{font-family:'Playfair Display',serif;font-size:32px}.rk-rating span{display:flex;gap:3px;color:#c49a2c;font-size:12px}.rk-rating small{color:var(--rk-muted);font-size:11px}.rk-testimonial-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:18px}.rk-testimonial-card{display:flex;flex-direction:column;min-height:260px;padding:26px;background:#fff;border:1px solid var(--rk-line);box-shadow:0 10px 30px rgba(23,53,43,.05)}.rk-testimonial-stars{display:flex;gap:4px;color:#c49a2c;font-size:11px}.rk-testimonial-card blockquote{flex:1;margin:22px 0 25px;color:var(--rk-ink);font-family:'Playfair Display',serif;font-size:17px;line-height:1.55}.rk-testimonial-author{display:flex;align-items:center;gap:11px;padding-top:16px;border-top:1px solid var(--rk-line)}.rk-testimonial-avatar{display:grid;place-items:center;width:38px;height:38px;border-radius:50%;background:var(--rk-green);color:var(--rk-lime);font-size:11px;font-weight:700}.rk-testimonial-author strong,.rk-testimonial-author small{display:block}.rk-testimonial-author strong{color:var(--rk-ink);font-size:12px}.rk-testimonial-author small{margin-top:3px;color:var(--rk-muted);font-size:10px}
    @media(max-width:900px){.rk-hero h1{font-size:55px}.rk-search-card{grid-template-columns:repeat(2,1fr)}.rk-search-title{grid-column:1/-1}.rk-car-grid-featured,.rk-car-grid-inventory{grid-template-columns:repeat(2,1fr)}.rk-benefits{grid-template-columns:repeat(2,1fr);gap:40px 20px}.rk-process-layout{gap:50px}.rk-testimonial-grid{grid-template-columns:1fr 1fr}.rk-testimonial-card:last-child{grid-column:1/-1}}
     @media(max-width:600px){.rk-container{padding:0 18px}.desktop-only{display:none}.rk-hero{height:690px}.rk-hero-image{background-position:62% center}.rk-hero h1{font-size:42px;letter-spacing:-1px}.rk-hero p{font-size:14px}.rk-hero-actions{flex-direction:column;align-items:stretch;width:205px}.rk-trust-row{gap:14px;margin-top:48px}.rk-trust-row strong{font-size:20px}.rk-trust-row span{font-size:9px}.rk-scroll{display:none}.rk-search-wrap{margin-top:-38px}.rk-search-card{grid-template-columns:1fr;padding:18px;gap:16px}.rk-search-title{grid-column:auto}.rk-search-submit{width:100%}.rk-section{padding:75px 0}.rk-section-head{display:block}.rk-section h2,.rk-process h2{font-size:35px}.rk-text-link,.rk-filter-link{display:inline-block;margin-top:22px}.rk-car-grid-featured,.rk-car-grid-inventory{grid-template-columns:1fr 1fr;gap:10px}.rk-car-photo{height:130px}.rk-car-body{padding:12px}.rk-car-body h3{font-size:12px;margin:7px 0}.rk-price{font-size:15px}.rk-wa-link{font-size:9px}.rk-heart{display:none}.rk-car-meta{font-size:8px}.rk-car-meta i{margin:0 2px}.rk-benefits{grid-template-columns:1fr 1fr;gap:30px 14px}.rk-benefit i{margin:23px 0 14px}.rk-benefit h3{font-size:13px}.rk-benefit p{font-size:11px}.rk-process{padding:75px 0}.rk-process-layout{grid-template-columns:1fr;gap:55px}.rk-testimonials{padding:75px 0}.rk-rating{margin-top:25px}.rk-testimonial-grid{grid-template-columns:1fr}.rk-testimonial-card:last-child{grid-column:auto}.rk-cta{padding:55px 0}.rk-cta-inner{display:block}.rk-cta h2{font-size:34px;margin-bottom:25px}.rk-button{font-size:12px;padding:13px 16px}}
    .rk-progress{position:fixed;top:0;left:0;right:0;height:3px;z-index:1100;pointer-events:none;background:rgba(23,53,43,.08)}
    .rk-progress span{display:block;height:100%;width:0;background:var(--rk-lime);box-shadow:0 0 12px rgba(216,237,104,.5);transition:width .12s linear}
    .rk-ticker{overflow:hidden;background:var(--rk-ink);color:rgba(255,255,255,.72);padding:14px 0}
    .rk-ticker-track{display:flex;width:max-content;animation:rk-ticker 24s linear infinite}
    .rk-ticker-group{display:flex;align-items:center;gap:30px;padding-right:30px}
    .rk-ticker-group span{font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;white-space:nowrap}
    .rk-ticker-group i{width:4px;height:4px;border-radius:50%;background:var(--rk-lime);flex:none}
    .rk-ticker:hover .rk-ticker-track{animation-play-state:paused}

    /* ===== MOTION ===== */
    @keyframes rk-fade-up{from{opacity:0;translate:0 26px}to{opacity:1;translate:0 0}}
    @keyframes rk-fade-in{from{opacity:0}to{opacity:1}}
    @keyframes rk-hero-drift{0%,100%{scale:1.06;translate:0 0}50%{scale:1.12;translate:-1.2% -1.2%}}
    @keyframes rk-bob{0%,100%{translate:0 0}50%{translate:0 7px}}
    @keyframes rk-orb{0%,100%{translate:0 0;scale:1}50%{translate:-18px 22px;scale:1.08}}
    @keyframes rk-line{0%,100%{width:28px}50%{width:46px}}
    @keyframes rk-gradient{0%,100%{background-position:0% 50%}50%{background-position:100% 50%}}
    @keyframes rk-pulse{0%,100%{opacity:1;box-shadow:0 0 0 0 rgba(29,91,66,.28)}50%{opacity:.55;box-shadow:0 0 0 4px rgba(29,91,66,0)}}
    @keyframes rk-ticker{to{transform:translateX(-16.6667%)}}

    .rk-motion .rk-hero-content > *{animation:rk-fade-up .9s cubic-bezier(.22,1,.36,1) both}
    .rk-motion .rk-hero-content > :nth-child(1){animation-delay:.08s}
    .rk-motion .rk-hero-content > :nth-child(2){animation-delay:.18s}
    .rk-motion .rk-hero-content > :nth-child(3){animation-delay:.3s}
    .rk-motion .rk-hero-content > :nth-child(4){animation-delay:.42s}
    .rk-motion .rk-hero-content > :nth-child(5){animation-delay:.58s}
    .rk-motion .rk-hero-image{animation:rk-hero-drift 24s ease-in-out infinite}
    .rk-motion .rk-hero-overlay{animation:rk-fade-in 1.1s ease .15s both}
    .rk-motion .rk-hero::before,.rk-motion .rk-hero::after{content:'';position:absolute;z-index:1;border-radius:50%;pointer-events:none;background:radial-gradient(circle,rgba(216,237,104,.18),transparent 68%);filter:blur(2px)}
    .rk-motion .rk-hero::before{width:420px;height:420px;right:-90px;top:12%;animation:rk-orb 14s ease-in-out infinite}
    .rk-motion .rk-hero::after{width:320px;height:320px;left:42%;bottom:-140px;background:radial-gradient(circle,rgba(216,237,104,.12),transparent 70%);animation:rk-orb 18s ease-in-out -6s infinite}
    .rk-motion .rk-eyebrow span{animation:rk-line 3.5s ease-in-out infinite}
    .rk-motion .rk-scroll{animation:rk-fade-in .8s ease .9s both}
    .rk-motion .rk-scroll i{animation:rk-bob 2.2s ease-in-out 1.7s infinite}
    .rk-motion .rk-cta{background:linear-gradient(120deg,#bad45b,#d8ef70,#a9c94f,#bad45b);background-size:300% 300%;animation:rk-gradient 14s ease infinite}
    .rk-motion .rk-status::before{content:'';display:inline-block;width:5px;height:5px;margin-right:5px;border-radius:50%;background:var(--rk-green);vertical-align:middle;animation:rk-pulse 1.8s ease-in-out infinite}
    .rk-motion .rk-button{position:relative;overflow:hidden}
    .rk-motion .rk-button::after{content:'';position:absolute;top:-20%;left:-80%;width:45%;height:140%;pointer-events:none;background:linear-gradient(90deg,transparent,rgba(255,255,255,.45),transparent);transform:skewX(-18deg);transition:left .55s ease}
    .rk-motion .rk-button:hover::after{left:130%}
    .rk-car-card.rk-tilting{transition:transform .15s ease-out,box-shadow .25s ease;will-change:transform}

    .rk-motion .rk-reveal{opacity:0;transition:opacity .75s cubic-bezier(.22,1,.36,1),translate .75s cubic-bezier(.22,1,.36,1),scale .75s cubic-bezier(.22,1,.36,1),transform .25s ease,box-shadow .25s ease}
    .rk-motion .rk-reveal-up{translate:0 30px}
    .rk-motion .rk-reveal-left{translate:-24px 0}
    .rk-motion .rk-reveal-right{translate:24px 0}
    .rk-motion .rk-reveal-zoom{translate:0 20px;scale:.94}
    .rk-motion .rk-text-link.rk-reveal,.rk-motion .rk-filter-link.rk-reveal{display:inline-block}
    .rk-motion .rk-reveal:not(.rk-visible){transition-delay:var(--rk-delay,0ms)}
    .rk-motion .rk-reveal.rk-visible{opacity:1;translate:0 0;scale:1}

    @media(max-width:600px){
        .rk-motion .rk-reveal-left,.rk-motion .rk-reveal-right{translate:0 26px}
    }

    .rk-trust-row strong{font-variant-numeric:tabular-nums}
    .rk-button i,.rk-text-link i,.rk-benefit i,.rk-status{transition:transform .3s ease}
    .rk-button:hover i{transform:translateX(3px)}
    .rk-text-link:hover i{transform:translateX(4px)}
    .rk-benefit:hover i{transform:translateY(-4px)}
    .rk-car-card:hover .rk-status{transform:translateY(-2px)}
    .rk-testimonial-card{transition:transform .3s ease,box-shadow .3s ease}
    .rk-testimonial-card:hover{transform:translateY(-4px);box-shadow:0 16px 34px rgba(23,53,43,.1)}
    .rk-search-submit{transition:transform .25s ease,box-shadow .25s ease}
    .rk-search-submit:hover{transform:translateY(-1px);box-shadow:0 8px 18px rgba(23,53,43,.18)}

    @media (prefers-reduced-motion: reduce){
        .rk-ticker{display:none}
        .rk-progress span{transition:none}
        .rk-motion .rk-hero-content > *,.rk-motion .rk-hero-image,.rk-motion .rk-hero-overlay,.rk-motion .rk-hero::before,.rk-motion .rk-hero::after,.rk-motion .rk-eyebrow span,.rk-motion .rk-scroll,.rk-motion .rk-scroll i{animation:none !important}
        .rk-motion .rk-reveal{opacity:1 !important;translate:none !important;scale:none !important;transition:none !important}
        .rk-motion .rk-cta{animation:none !important;background:#bad45b !important}
        .rk-motion .rk-status::before{animation:none !important}
        .rk-motion .rk-button::after{display:none !important}
        .rk-button,.rk-button i,.rk-text-link i,.rk-benefit i,.rk-status,.rk-car-photo img,.rk-car-card,.rk-testimonial-card,.rk-search-submit,.rk-social-links a{transition:none !important}
    }
</style>

<script>
    (() => {
        const progressBar = document.querySelector('.rk-progress span');

        if (progressBar) {
            let progressFrame = null;
            const updateProgress = () => {
                if (progressFrame) {
                    return;
                }

                progressFrame = requestAnimationFrame(() => {
                    const max = document.documentElement.scrollHeight - window.innerHeight;
                    const value = max > 0 ? (window.scrollY / max) * 100 : 0;
                    progressBar.style.width = `${Math.min(value, 100)}%`;
                    progressFrame = null;
                });
            };

            updateProgress();
            window.addEventListener('scroll', updateProgress, { passive: true });
            window.addEventListener('resize', updateProgress);
            window.addEventListener('load', updateProgress);
        }

        if (!document.documentElement.classList.contains('rk-motion')) {
            return;
        }

        const revealConfigs = [
            { selector: '.rk-search-card', variant: 'zoom' },
            { selector: '.rk-ticker', variant: 'up' },
            { selector: '.rk-section-head > div:first-child > *', variant: 'up' },
            { selector: '.rk-section-head > a', variant: 'right' },
            { selector: '.rk-section-head > .rk-head-copy', variant: 'right' },
            { selector: '.rk-rating', variant: 'up' },
            { selector: '.rk-car-card', variants: ['up', 'zoom'] },
            { selector: '.rk-benefit', variants: ['up', 'zoom', 'up', 'zoom'] },
            { selector: '.rk-steps > div', variant: 'right' },
            { selector: '.rk-process-layout > div:first-child > *', variant: 'left' },
            { selector: '.rk-testimonial-card', variants: ['up', 'zoom', 'up'] },
            { selector: '.rk-cta-inner > div:first-child > *', variant: 'left' },
            { selector: '.rk-cta-inner > div:last-child > *', variant: 'right' },
            { selector: '.rk-empty', variant: 'zoom' },
        ];

        const revealTargets = [];
        const seenTargets = new Set();
        const groupIndex = new Map();

        revealConfigs.forEach(({ selector, variant, variants }) => {
            document.querySelectorAll(selector).forEach((el, index) => {
                if (seenTargets.has(el)) {
                    return;
                }

                seenTargets.add(el);
                const chosenVariant = variants ? variants[index % variants.length] : variant;
                el.classList.add('rk-reveal', `rk-reveal-${chosenVariant}`);

                const parent = el.parentElement;
                const group = groupIndex.get(parent) || 0;
                groupIndex.set(parent, group + 1);
                el.style.setProperty('--rk-delay', `${Math.min(group, 5) * 70}ms`);

                revealTargets.push(el);
            });
        });

        if (!('IntersectionObserver' in window)) {
            revealTargets.forEach((el) => el.classList.add('rk-visible'));
        } else {
            const revealObserver = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('rk-visible');
                        revealObserver.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

            revealTargets.forEach((el) => revealObserver.observe(el));
        }

        if (window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
            document.querySelectorAll('.rk-car-card').forEach((card) => {
                card.addEventListener('pointerenter', () => card.classList.add('rk-tilting'));
                card.addEventListener('pointermove', (event) => {
                    const rect = card.getBoundingClientRect();
                    const x = (event.clientX - rect.left) / rect.width - 0.5;
                    const y = (event.clientY - rect.top) / rect.height - 0.5;
                    card.style.transform = `perspective(900px) translateY(-4px) rotateX(${(-y * 4).toFixed(2)}deg) rotateY(${(x * 4).toFixed(2)}deg)`;
                });
                card.addEventListener('pointerleave', () => {
                    card.classList.remove('rk-tilting');
                    card.style.transform = '';
                });
            });
        }

        const trustRow = document.querySelector('.rk-trust-row');
        const counters = trustRow ? Array.from(trustRow.querySelectorAll('strong')) : [];

        const animateCounter = (el) => {
            const raw = el.textContent.trim();
            const match = raw.match(/^([\d.,]+)(.*)$/);
            if (!match) {
                return;
            }

            const target = Number(match[1].replace(',', '.'));
            const suffix = match[2];
            const decimals = (match[1].split(/[.,]/)[1] || '').length;
            const duration = 1100;
            const start = performance.now();

            const tick = (now) => {
                const progress = Math.min((now - start) / duration, 1);
                const eased = 1 - Math.pow(1 - progress, 3);
                el.textContent = `${(target * eased).toFixed(decimals)}${suffix}`;
                if (progress < 1) {
                    requestAnimationFrame(tick);
                }
            };

            requestAnimationFrame(tick);
        };

        const startCounters = () => counters.forEach(animateCounter);

        if (counters.length && trustRow && 'IntersectionObserver' in window) {
            const counterObserver = new IntersectionObserver((entries) => {
                if (entries.some((entry) => entry.isIntersecting)) {
                    startCounters();
                    counterObserver.disconnect();
                }
            }, { threshold: 0.4 });

            counterObserver.observe(trustRow);
        } else {
            startCounters();
        }
    })();
</script>
