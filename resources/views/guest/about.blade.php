@php
    $siteName = $settings['site_name'] ?? 'RodaTa';
    $phone = $settings['phone_number'] ?? '081234567890';
    $whatsapp = $settings['whatsapp_number'] ?? '6281234567890';
    $whatsappUrl = 'https://wa.me/'.$whatsapp.'?text='.rawurlencode('Halo '.$siteName.', saya ingin konsultasi jual beli mobil bekas.');
@endphp

<x-layouts.guest
    :title="'Tentang '.$siteName.' — Jual Beli Mobil Bekas Terpercaya'"
    :description="'Kenali '.$siteName.', showroom jual beli mobil bekas yang mengutamakan kualitas, transparansi, dan proses yang mudah.'"
    :settings="$settings"
>
    <div class="about-page">
        <section class="about-hero">
            <div class="about-hero-glow about-hero-glow-one"></div>
            <div class="about-hero-glow about-hero-glow-two"></div>
            <div class="about-container about-hero-inner">
                <div class="about-kicker"><span></span> TENTANG {{ strtoupper($siteName) }}</div>
                <h1>Teman jual beli<br>mobil <span>bekasmu.</span></h1>
                <p>Kami membantu kamu menjual mobil lama atau menemukan mobil bekas berkualitas — dengan informasi jujur, harga jelas, dan proses yang terasa mudah.</p>
                <a href="{{ route('home') }}#stok" class="about-button">Lihat stok mobil <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="about-hero-note"><i class="fas fa-shield-halved"></i><span>Diperiksa dengan teliti<br>untuk pembeli & penjual</span></div>
        </section>

        <section class="about-story">
            <div class="about-container about-story-grid">
                <div class="about-story-intro about-reveal">
                    <div class="about-kicker">CERITA KAMI</div>
                    <h2>Jual atau beli mobil,<br>tanpa rasa <span>ragu.</span></h2>
                </div>
                <div class="about-story-copy about-reveal">
                    <p>{{ $siteName }} lahir dari satu keyakinan sederhana: jual beli mobil bekas tidak seharusnya membuat orang ragu atau merasa terburu-buru.</p>
                    <p>Kami memilih setiap unit dengan teliti, menyampaikan kondisi apa adanya, dan mendampingi kamu sejak konsultasi, penilaian, sampai mobil siap digunakan atau terjual.</p>
                    <p>Karena bagi kami, transaksi bukan akhir perjalanan. Hubungan baik dengan pelanggan adalah bagian dari layanan.</p>
                </div>
            </div>
        </section>

        <section class="about-numbers">
            <div class="about-container about-number-grid">
                <div class="about-number about-reveal"><strong>10<span>+</span></strong><small>Tahun pengalaman</small></div>
                <div class="about-number about-reveal"><strong>500<span>+</span></strong><small>Unit terjual</small></div>
                <div class="about-number about-reveal"><strong>4.9<span>/5</span></strong><small>Rating pelanggan</small></div>
                <div class="about-number about-reveal"><strong>100<span>%</span></strong><small>Harga transparan</small></div>
            </div>
        </section>

        <section class="about-values">
            <div class="about-container">
                <div class="about-section-heading about-reveal">
                    <div class="about-kicker">NILAI YANG KAMI PEGANG</div>
                    <h2>Alasan pelanggan<br>datang kembali.</h2>
                </div>
                <div class="about-value-grid">
                    <article class="about-value about-reveal"><span>01</span><i class="fas fa-eye"></i><h3>Terbuka sejak awal</h3><p>Detail unit, kondisi, dan harga kami sampaikan dengan jelas tanpa menyembunyikan hal penting.</p></article>
                    <article class="about-value about-reveal"><span>02</span><i class="fas fa-clipboard-check"></i><h3>Dipilih dengan teliti</h3><p>Setiap mobil melewati proses seleksi dan pemeriksaan sebelum kami rekomendasikan.</p></article>
                    <article class="about-value about-reveal"><span>03</span><i class="fas fa-handshake"></i><h3>Dilayani dengan tulus</h3><p>Kami mendengar kebutuhanmu lalu memberi rekomendasi yang sesuai, bukan sekadar mengejar transaksi.</p></article>
                    <article class="about-value about-reveal"><span>04</span><i class="fas fa-road"></i><h3>Mendampingi sampai selesai</h3><p>Dari konsultasi, test drive, dokumen, sampai serah terima, kami tetap siap membantu.</p></article>
                </div>
            </div>
        </section>

        <section class="about-steps">
            <div class="about-container about-steps-grid">
                <div class="about-reveal"><div class="about-kicker">CARA KAMI BEKERJA</div><h2>Lebih tenang<br>jual atau beli.</h2><p>Kami membuat setiap tahap terasa jelas, sehingga kamu bisa mengambil keputusan dengan percaya diri.</p></div>
                <div class="about-step-list">
                    <div class="about-step about-reveal"><b>01</b><span><strong>Dengarkan kebutuhanmu</strong><small>Mau membeli atau menjual? Budget, kondisi mobil, dan rencanamu jadi awal kami membantu.</small></span></div>
                    <div class="about-step about-reveal"><b>02</b><span><strong>Kurasi unit atau nilai mobil</strong><small>Kami cocokkan kebutuhanmu dengan unit tersedia atau beri penilaian untuk mobil yang ingin dijual.</small></span></div>
                    <div class="about-step about-reveal"><b>03</b><span><strong>Temani sampai selesai</strong><small>Inspeksi, test drive, pembayaran, dan dokumen kami bantu sampai proses selesai.</small></span></div>
                </div>
            </div>
        </section>

        <section class="about-contact">
            <div class="about-container about-contact-inner about-reveal">
                <div><div class="about-kicker">SIAP MEMULAI?</div><h2>Siap jual atau beli<br>mobil bekas?</h2></div>
                <div><p>Ceritakan kebutuhanmu. Tim kami siap membantu pembelian maupun penjualan tanpa tekanan.</p><a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer" class="about-button about-button-light"><i class="fab fa-whatsapp"></i> Konsultasi gratis</a><a href="tel:{{ $phone }}" class="about-phone"><i class="fas fa-phone"></i> {{ $phone }}</a></div>
            </div>
        </section>
    </div>

    @push('styles')
    <style>
        .about-page{background:#f5f7f5;color:#17352b;overflow:hidden}.about-container{max-width:1160px;margin:auto;padding:0 28px}.about-kicker{font-size:11px;font-weight:700;letter-spacing:2.2px;color:#79951d}.about-kicker span{display:inline-block;width:28px;height:1px;background:#d8ed68;vertical-align:middle;margin-right:10px}.about-hero{min-height:620px;padding:170px 0 110px;background:#112c24;color:#fff;position:relative;overflow:hidden}.about-hero-inner{position:relative;z-index:2}.about-hero h1{font:800 clamp(42px,6vw,68px)/1.05 'Plus Jakarta Sans',sans-serif;letter-spacing:-2.4px;margin:24px 0 26px}.about-hero p{max-width:530px;color:rgba(255,255,255,.72);font-size:17px;line-height:1.75;margin:0 0 32px}.about-button{display:inline-flex;align-items:center;gap:12px;padding:14px 21px;background:#d8ed68;color:#17352b;text-decoration:none;font-size:13px;font-weight:700;border-radius:3px;transition:transform .25s,box-shadow .25s}.about-button:hover{color:#17352b;transform:translateY(-3px);box-shadow:0 12px 24px rgba(0,0,0,.16)}.about-button i{transition:transform .25s}.about-button:hover i{transform:translateX(4px)}.about-hero-glow{position:absolute;border-radius:50%;pointer-events:none;background:radial-gradient(circle,rgba(216,237,104,.18),transparent 68%);filter:blur(2px)}.about-hero-glow-one{width:520px;height:520px;right:-140px;top:-80px}.about-hero-glow-two{width:300px;height:300px;left:44%;bottom:-190px;opacity:.7}.about-hero-note{position:absolute;z-index:2;right:max(28px,calc((100vw - 1104px)/2));bottom:48px;display:flex;align-items:center;gap:12px;color:rgba(255,255,255,.72);font-size:11px;line-height:1.5}.about-hero-note i{color:#d8ed68;font-size:20px}.about-story{padding:115px 0;background:#fff}.about-story-grid{display:grid;grid-template-columns:1fr 1fr;gap:100px}.about-story h2,.about-section-heading h2,.about-steps h2,.about-contact h2{font:700 42px/1.15 'Playfair Display',serif;letter-spacing:-.8px;margin:13px 0 0}.about-story h2 span{color:inherit}.about-story-copy{padding-top:28px}.about-story-copy p{color:#71817a;font-size:15px;line-height:1.85;margin:0 0 18px}.about-numbers{background:#1d5b42;color:#fff;padding:48px 0}.about-number-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:20px}.about-number{border-left:1px solid rgba(255,255,255,.25);padding-left:18px}.about-number strong{display:block;font:700 38px 'Playfair Display',serif}.about-number strong span{color:#d8ed68;font-size:23px}.about-number small{color:rgba(255,255,255,.62);font-size:11px}.about-values{padding:115px 0;background:#f5f7f5}.about-section-heading{margin-bottom:40px}.about-value-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:20px}.about-value{border-top:1px solid #b8c9c0;padding-top:18px}.about-value>span{font-size:10px;color:#1d5b42}.about-value i{display:block;color:#1d5b42;font-size:25px;margin:32px 0 20px}.about-value h3{font-size:15px;margin:0 0 9px}.about-value p{color:#71817a;font-size:12px;line-height:1.7;margin:0}.about-steps{padding:100px 0;background:#1d5b42;color:#fff}.about-steps-grid{display:grid;grid-template-columns:1fr 1fr;gap:120px;align-items:center}.about-steps h2{color:#fff}.about-steps p{max-width:310px;color:rgba(255,255,255,.66);font-size:13px;line-height:1.75;margin-top:24px}.about-step-list{border-top:1px solid rgba(255,255,255,.22)}.about-step{display:flex;gap:22px;padding:22px 0;border-bottom:1px solid rgba(255,255,255,.22)}.about-step b{font:400 20px 'Playfair Display',serif;color:#d8ed68}.about-step span{display:flex;flex-direction:column;gap:5px}.about-step small{color:rgba(255,255,255,.56);font-size:11px;line-height:1.6}.about-contact{padding:75px 0;background:#bad45b}.about-contact-inner{display:flex;justify-content:space-between;align-items:center;gap:40px}.about-contact h2{font-size:38px}.about-contact p{max-width:280px;color:#426342;font-size:13px;line-height:1.6;margin:0 0 18px}.about-button-light{background:#fff;color:#1d5b42}.about-button-light:hover{color:#1d5b42}.about-phone{display:block;margin-top:15px;color:#376348;text-decoration:none;font-size:12px}.about-phone i{margin-right:5px}
        .about-reveal{opacity:0;translate:0 26px;transition:opacity .7s cubic-bezier(.22,1,.36,1),translate .7s cubic-bezier(.22,1,.36,1)}.about-reveal.about-visible{opacity:1;translate:0 0}.about-value:nth-child(even){transition-delay:80ms}.about-number:nth-child(2){transition-delay:80ms}.about-number:nth-child(3){transition-delay:160ms}.about-number:nth-child(4){transition-delay:240ms}
        @media(max-width:800px){.about-story-grid,.about-steps-grid{grid-template-columns:1fr;gap:45px}.about-number-grid{grid-template-columns:repeat(2,1fr);gap:30px 20px}.about-value-grid{grid-template-columns:repeat(2,1fr);gap:38px 20px}.about-hero-note{position:relative;right:auto;bottom:auto;margin:48px 28px 0}.about-contact-inner{display:block}.about-contact h2{margin-bottom:28px}}
        @media(max-width:600px){.about-container{padding:0 18px}.about-hero{min-height:670px;padding:140px 0 70px}.about-hero h1{font-size:38px;letter-spacing:-1.4px}.about-hero p{font-size:14px}.about-story,.about-values{padding:75px 0}.about-story h2,.about-section-heading h2,.about-steps h2{font-size:35px}.about-number strong{font-size:29px}.about-value-grid{gap:30px 14px}.about-value h3{font-size:13px}.about-value p{font-size:11px}.about-steps{padding:75px 0}.about-contact{padding:55px 0}.about-contact h2{font-size:34px}}
        @media(prefers-reduced-motion:reduce){.about-reveal{opacity:1;translate:none;transition:none}.about-button,.about-button i{transition:none}}
    </style>
    @endpush

    @push('scripts')
    <script>
        (() => {
            const items = document.querySelectorAll('.about-reveal');
            if (!items.length || window.matchMedia('(prefers-reduced-motion: reduce)').matches || !('IntersectionObserver' in window)) {
                items.forEach((item) => item.classList.add('about-visible'));
                return;
            }
            const observer = new IntersectionObserver((entries) => entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('about-visible');
                    observer.unobserve(entry.target);
                }
            }), { threshold: .14, rootMargin: '0px 0px -35px' });
            items.forEach((item) => observer.observe(item));
        })();
    </script>
    @endpush
</x-layouts.guest>
