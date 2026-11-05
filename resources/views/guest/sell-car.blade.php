@php
    $siteName = $settings['site_name'] ?? 'RodaTa';
    $phone = $settings['phone_number'] ?? '081234567890';
    $whatsapp = $settings['whatsapp_number'] ?? '6281234567890';
    $whatsappUrl = 'https://wa.me/'.$whatsapp.'?text='.rawurlencode('Halo '.$siteName.', saya ingin menjual mobil.');
    $currentYear = now()->year;
@endphp

<x-layouts.guest
    :title="'Jual Mobil ke '.$siteName.' — Proses Cepat & Transparan'"
    :description="'Jual mobil bekas kamu ke '.$siteName.'. Isi informasi mobil, tim kami akan menghubungi kamu melalui WhatsApp.'"
    :settings="$settings"
>
    <div class="sell-page">
        <section class="sell-hero">
            <div class="sell-hero-glow sell-hero-glow-one"></div>
            <div class="sell-hero-glow sell-hero-glow-two"></div>
            <div class="sell-container sell-hero-inner">
                <div class="sell-kicker"><span></span> JUAL MOBILMU</div>
                <h1>Mobil ingin dijual?<br>Kami bantu <span>prosesnya.</span></h1>
                <p>Isi informasi mobilmu di bawah ini. Tim {{ $siteName }} akan menghubungi kamu melalui WhatsApp untuk penilaian dan langkah selanjutnya.</p>
                <a href="#form-jual" class="sell-button">Isi informasi mobil <i class="fas fa-arrow-down"></i></a>
            </div>
            <div class="sell-hero-note"><i class="fab fa-whatsapp"></i><span>Dibalas melalui WhatsApp<br>tanpa biaya konsultasi</span></div>
        </section>

        <section class="sell-section" id="form-jual">
            <div class="sell-container sell-grid">
                <aside class="sell-side">
                    <div class="sell-kicker">CARA KERJANYA</div>
                    <h2>Jual mobil<br>lebih tenang.</h2>
                    <p>Kami membuat proses penjualan mobil bekas terasa sederhana dan transparan, dari penilaian sampai kesepakatan harga.</p>

                    <div class="sell-steps">
                        <div class="sell-step">
                            <b>01</b>
                            <span><strong>Isi informasi mobil</strong><small>Lengkapi data singkat kendaraan yang ingin kamu jual.</small></span>
                        </div>
                        <div class="sell-step">
                            <b>02</b>
                            <span><strong>Dihubungi via WhatsApp</strong><small>Tim kami menindaklanjuti dan menjadwalkan penilaian.</small></span>
                        </div>
                        <div class="sell-step">
                            <b>03</b>
                            <span><strong>Inspeksi & penawaran</strong><small>Kami periksa kondisi unit lalu ajukan harga terbaik.</small></span>
                        </div>
                        <div class="sell-step">
                            <b>04</b>
                            <span><strong>Deal & dokumen</strong><small>Proses jual beli dan dokumen dibantu sampai selesai.</small></span>
                        </div>
                    </div>

                    <div class="sell-side-cta">
                        <span>Ingin langsung bertanya?</span>
                        <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer"><i class="fab fa-whatsapp"></i> Chat tim kami</a>
                    </div>
                </aside>

                <div class="sell-card">
                    <div class="sell-card-head">
                        <div>
                            <div class="sell-kicker">INFORMASI MOBIL</div>
                            <h2>Ceritakan mobilmu</h2>
                        </div>
                        <span class="sell-card-icon"><i class="fas fa-car-side"></i></span>
                    </div>

                    @if ($errors->any())
                        <div class="sell-alert">
                            <i class="fas fa-circle-exclamation"></i>
                            <div>
                                <strong>Periksa kembali data berikut:</strong>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('sell-car.store') }}" method="POST" class="sell-form">
                        @csrf

                        <div class="sell-form-section">
                            <h3><span>01</span> Data penjual</h3>
                            <div class="sell-form-grid">
                                <label class="sell-field">
                                    <span>Nama lengkap <em>*</em></span>
                                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Nama kamu" required autocomplete="name">
                                    @error('name') <small class="sell-error">{{ $message }}</small> @enderror
                                </label>
                                <label class="sell-field">
                                    <span>Nomor WhatsApp <em>*</em></span>
                                    <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx" required autocomplete="tel">
                                    @error('phone') <small class="sell-error">{{ $message }}</small> @enderror
                                </label>
                                <label class="sell-field sell-field-full">
                                    <span>Email <small>(opsional)</small></span>
                                    <input type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" autocomplete="email">
                                    @error('email') <small class="sell-error">{{ $message }}</small> @enderror
                                </label>
                            </div>
                        </div>

                        <div class="sell-form-section">
                            <h3><span>02</span> Data mobil</h3>
                            <div class="sell-form-grid">
                                <label class="sell-field">
                                    <span>Merek <em>*</em></span>
                                    <input type="text" name="brand" value="{{ old('brand') }}" placeholder="Toyota, Honda, Suzuki..." required>
                                    @error('brand') <small class="sell-error">{{ $message }}</small> @enderror
                                </label>
                                <label class="sell-field">
                                    <span>Model <em>*</em></span>
                                    <input type="text" name="model" value="{{ old('model') }}" placeholder="Avanza, Brio, Ertiga..." required>
                                    @error('model') <small class="sell-error">{{ $message }}</small> @enderror
                                </label>
                                <label class="sell-field">
                                    <span>Tahun <em>*</em></span>
                                    <input type="number" name="year" value="{{ old('year') }}" placeholder="{{ $currentYear }}" min="1900" max="{{ $currentYear + 1 }}" required>
                                    @error('year') <small class="sell-error">{{ $message }}</small> @enderror
                                </label>
                                <label class="sell-field">
                                    <span>Transmisi</span>
                                    <select name="transmission">
                                        <option value="">Pilih transmisi</option>
                                        @foreach ($transmissions as $transmission)
                                            <option value="{{ $transmission->value }}" @selected(old('transmission') === $transmission->value)>{{ $transmission->label() }}</option>
                                        @endforeach
                                    </select>
                                    @error('transmission') <small class="sell-error">{{ $message }}</small> @enderror
                                </label>
                                <label class="sell-field">
                                    <span>Tipe bodi</span>
                                    <select name="body_type">
                                        <option value="">Pilih tipe bodi</option>
                                        @foreach ($bodyTypes as $bodyType)
                                            <option value="{{ $bodyType->value }}" @selected(old('body_type') === $bodyType->value)>{{ $bodyType->label() }}</option>
                                        @endforeach
                                    </select>
                                    @error('body_type') <small class="sell-error">{{ $message }}</small> @enderror
                                </label>
                                <label class="sell-field">
                                    <span>Jarak tempuh (km)</span>
                                    <input type="number" name="mileage" value="{{ old('mileage') }}" placeholder="Contoh: 45000" min="0" max="2000000">
                                    @error('mileage') <small class="sell-error">{{ $message }}</small> @enderror
                                </label>
                                <label class="sell-field sell-field-full">
                                    <span>Harga yang diharapkan <small>(opsional)</small></span>
                                    <div class="sell-price-input">
                                        <span>Rp</span>
                                        <input type="number" name="expected_price" value="{{ old('expected_price') }}" placeholder="Contoh: 150000000" min="0" step="1000000">
                                    </div>
                                    @error('expected_price') <small class="sell-error">{{ $message }}</small> @enderror
                                </label>
                                <label class="sell-field sell-field-full">
                                    <span>Kondisi & catatan tambahan <small>(opsional)</small></span>
                                    <textarea name="condition" rows="4" placeholder="Ceritakan kondisi mobil, riwayat servis, atau hal lain yang perlu kami ketahui.">{{ old('condition') }}</textarea>
                                    @error('condition') <small class="sell-error">{{ $message }}</small> @enderror
                                </label>
                            </div>
                        </div>

                        <label class="sell-consent">
                            <input type="checkbox" name="consent" value="1" @checked(old('consent')) required>
                            <span>Saya setuju dihubungi oleh {{ $siteName }} melalui WhatsApp untuk membahas penjualan mobil saya.</span>
                        </label>
                        @error('consent') <small class="sell-error">{{ $message }}</small> @enderror

                        <button type="submit" class="sell-submit">
                            <i class="fab fa-whatsapp"></i> Kirim & lanjut ke WhatsApp
                        </button>
                        <p class="sell-form-note"><i class="fas fa-lock"></i> Informasi kamu hanya digunakan untuk menindaklanjuti penjualan mobil.</p>
                    </form>
                </div>
            </div>
        </section>
    </div>

    @push('styles')
    <style>
        .sell-page{background:#f5f7f5;color:#17352b;overflow:hidden}
        .sell-container{max-width:1160px;margin:auto;padding:0 28px}
        .sell-kicker{font-size:11px;font-weight:700;letter-spacing:2.2px;color:#79951d}
        .sell-kicker span{display:inline-block;width:28px;height:1px;background:#d8ed68;vertical-align:middle;margin-right:10px}
        .sell-hero{min-height:560px;padding:170px 0 100px;background:#112c24;color:#fff;position:relative;overflow:hidden}
        .sell-hero-inner{position:relative;z-index:2}
        .sell-hero h1{font:800 clamp(40px,6vw,64px)/1.05 'Plus Jakarta Sans',sans-serif;letter-spacing:-2.4px;margin:24px 0 24px}
        .sell-hero h1 span{color:#d8ed68}
        .sell-hero p{max-width:560px;color:rgba(255,255,255,.72);font-size:17px;line-height:1.75;margin:0 0 32px}
        .sell-button{display:inline-flex;align-items:center;gap:12px;padding:14px 21px;background:#d8ed68;color:#17352b;text-decoration:none;font-size:13px;font-weight:700;border-radius:3px;transition:transform .25s,box-shadow .25s}
        .sell-button:hover{color:#17352b;transform:translateY(-3px);box-shadow:0 12px 24px rgba(0,0,0,.16)}
        .sell-hero-glow{position:absolute;border-radius:50%;pointer-events:none;background:radial-gradient(circle,rgba(216,237,104,.18),transparent 68%);filter:blur(2px)}
        .sell-hero-glow-one{width:520px;height:520px;right:-140px;top:-80px}
        .sell-hero-glow-two{width:300px;height:300px;left:44%;bottom:-190px;opacity:.7}
        .sell-hero-note{position:absolute;z-index:2;right:max(28px,calc((100vw - 1104px)/2));bottom:44px;display:flex;align-items:center;gap:12px;color:rgba(255,255,255,.72);font-size:11px;line-height:1.5}
        .sell-hero-note i{color:#d8ed68;font-size:22px}

        .sell-section{padding:95px 0 110px;background:#fff}
        .sell-grid{display:grid;grid-template-columns:minmax(0,.82fr) minmax(0,1.18fr);gap:64px;align-items:start}
        .sell-side h2{font:700 42px/1.15 'Playfair Display',serif;letter-spacing:-.8px;margin:13px 0 20px}
        .sell-side>p{color:#6b7f76;font-size:14px;line-height:1.8;margin:0 0 32px;max-width:360px}
        .sell-steps{display:grid;gap:22px;margin-bottom:34px}
        .sell-step{display:flex;gap:16px}
        .sell-step b{font:700 13px/1 'DM Sans',sans-serif;color:#79951d;padding-top:2px;min-width:24px}
        .sell-step strong{display:block;font-size:14px;font-weight:700;margin-bottom:4px}
        .sell-step small{display:block;color:#6b7f76;font-size:12px;line-height:1.6}
        .sell-side-cta{padding-top:26px;border-top:1px solid #dce6e1}
        .sell-side-cta span{display:block;font-size:12px;color:#6b7f76;margin-bottom:10px}
        .sell-side-cta a{display:inline-flex;align-items:center;gap:9px;color:#1d5b42;font-size:13px;font-weight:700;text-decoration:none}
        .sell-side-cta a:hover{color:#17352b}
        .sell-side-cta a i{font-size:16px}

        .sell-card{background:#fff;border:1px solid #dce6e1;border-radius:8px;padding:38px;box-shadow:0 24px 60px rgba(23,53,43,.08)}
        .sell-card-head{display:flex;align-items:flex-start;justify-content:space-between;gap:20px;margin-bottom:28px}
        .sell-card-head h2{font:700 28px/1.2 'Playfair Display',serif;letter-spacing:-.4px;margin:8px 0 0}
        .sell-card-icon{width:52px;height:52px;flex:none;border-radius:50%;display:grid;place-items:center;background:#eef4e3;color:#1d5b42;font-size:20px}

        .sell-alert{display:flex;gap:12px;padding:16px 18px;margin-bottom:24px;background:#fdf3e7;border:1px solid #f2d9b0;border-radius:6px;color:#8a5a13;font-size:12px;line-height:1.6}
        .sell-alert i{margin-top:2px;font-size:15px}
        .sell-alert strong{display:block;margin-bottom:4px}
        .sell-alert ul{margin:0;padding-left:16px}

        .sell-form-section{margin-bottom:28px}
        .sell-form-section h3{display:flex;align-items:center;gap:10px;font:700 13px/1 'DM Sans',sans-serif;letter-spacing:.4px;text-transform:uppercase;color:#17352b;margin:0 0 18px}
        .sell-form-section h3 span{display:inline-grid;place-items:center;width:24px;height:24px;border-radius:50%;background:#1d5b42;color:#fff;font-size:10px;letter-spacing:0}
        .sell-form-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
        .sell-field{display:flex;flex-direction:column;gap:7px}
        .sell-field-full{grid-column:1/-1}
        .sell-field>span{font-size:12px;font-weight:600;color:#17352b}
        .sell-field>span em{color:#c0563a;font-style:normal}
        .sell-field>span small{color:#91a199;font-weight:400}
        .sell-field input,.sell-field select,.sell-field textarea{width:100%;padding:11px 13px;border:1px solid #dce6e1;border-radius:5px;background:#fbfcfb;font:400 13px 'DM Sans',sans-serif;color:#17352b;transition:border-color .2s,box-shadow .2s}
        .sell-field input:focus,.sell-field select:focus,.sell-field textarea:focus{outline:none;border-color:#1d5b42;box-shadow:0 0 0 3px rgba(29,91,66,.12);background:#fff}
        .sell-field textarea{resize:vertical;min-height:96px}
        .sell-price-input{position:relative;display:flex;align-items:center}
        .sell-price-input>span{position:absolute;left:13px;font-size:12px;font-weight:600;color:#6b7f76}
        .sell-price-input input{padding-left:38px}
        .sell-error{color:#c0563a;font-size:11px;line-height:1.4}
        .sell-consent{display:flex;align-items:flex-start;gap:10px;font-size:12px;line-height:1.6;color:#6b7f76;margin-bottom:6px;cursor:pointer}
        .sell-consent input{margin-top:2px;width:16px;height:16px;flex:none;accent-color:#1d5b42}
        .sell-submit{display:flex;align-items:center;justify-content:center;gap:10px;width:100%;margin-top:20px;padding:14px 20px;border:none;border-radius:5px;background:#1d5b42;color:#fff;font:700 14px 'DM Sans',sans-serif;cursor:pointer;transition:background .2s,transform .2s}
        .sell-submit:hover{background:#17352b;transform:translateY(-1px)}
        .sell-submit i{font-size:18px}
        .sell-form-note{display:flex;align-items:center;justify-content:center;gap:7px;margin:14px 0 0;color:#91a199;font-size:11px}

        @media(max-width:900px){
            .sell-grid{grid-template-columns:1fr;gap:48px}
            .sell-side>p{max-width:none}
        }
        @media(max-width:600px){
            .sell-container{padding:0 18px}
            .sell-hero{min-height:620px;padding:140px 0 70px}
            .sell-hero h1{font-size:37px;letter-spacing:-1.4px}
            .sell-hero p{font-size:14px}
            .sell-hero-note{position:relative;right:auto;bottom:auto;margin:44px 18px 0}
            .sell-section{padding:70px 0 80px}
            .sell-side h2{font-size:34px}
            .sell-card{padding:24px 20px}
            .sell-form-grid{grid-template-columns:1fr}
            .sell-card-head h2{font-size:24px}
            .sell-card-icon{width:44px;height:44px;font-size:17px}
        }
    </style>
    @endpush
</x-layouts.guest>
