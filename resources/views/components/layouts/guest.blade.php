@props([
    'title' => 'RodaTa — Mobil Bekas Terpercaya',
    'description' => 'Temukan mobil bekas berkualitas dengan harga transparan dan proses mudah di RodaTa.',
    'type' => 'guest',
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $description }}">
    <title>{{ $title }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/fontawesome-free-7.2.0-web/css/all.min.css')}}">

    <link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css')}}">

        <style>
            :root { --bs-primary: #1d5b42; --primary-light: #e8f1ed; --text-primary: #17352b; --text-secondary: #6b7f76; --text-muted: #91a199; --border-color: #dbe5df; --bg-light: #f5f7f5; --bg-white: #fff; }
        :root {
            --bs-primary: #6366f1;
            --bs-primary: #4f46e5;
            --primary-light: #818cf8;
            --bs-secondary: #0ea5e9;
            --bs-success: #10b981;
            --bs-warning: #f59e0b;
            --bs-danger: #ef4444;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --text-muted: #94a3b8;
            --border-color: #e2e8f0;
            --bg-light: #f8fafc;
            --bg-white: #ffffff;
        }

        [data-theme="dark"] {
            --bs-primary: #818cf8;
            --bs-primary: #6366f1;
            --primary-light: #a5b4fc;
            --text-primary: #f1f5f9;
            --text-secondary: #cbd5e1;
            --text-muted: #94a3b8;
            --border-color: #334155;
            --bg-light: #0f172a;
            --bg-white: #1e293b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--bg-light);
            color: var(--text-primary);
            line-height: 1.6;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* ===== NAVBAR (shared across all pages) ===== */
        .site-navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.94);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        [data-theme="dark"] .site-navbar {
            background: rgba(15, 23, 42, 0.95);
        }

        .site-navbar-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .site-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: var(--text-primary);
            font-size: 1.2rem;
            font-weight: 700;
        }

        .site-brand i {
            font-size: 1.5rem;
            color: var(--bs-primary);
        }

        .site-nav {
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 2rem;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .site-nav-link {
            color: var(--text-secondary);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .site-nav-link:hover {
            color: var(--bs-primary);
        }

        .site-navbar-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .btn-nav {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.95rem;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
        }

        .btn-nav-outline {
            background: transparent;
            border: 2px solid var(--border-color);
            color: var(--text-primary);
        }

        .btn-nav-outline:hover {
            border-color: var(--bs-primary);
            color: var(--bs-primary);
        }

        .btn-nav-primary {
            background: linear-gradient(135deg, var(--bs-primary) 0%, var(--bs-primary) 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
        }

        .btn-nav-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4);
            color: white;
        }

        /* Theme Toggle */
        .theme-toggle {
            background: transparent;
            border: none;
            color: var(--text-secondary);
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .theme-toggle:hover {
            background: var(--border-color);
            color: var(--bs-primary);
        }

        .theme-toggle i {
            font-size: 1.25rem;
        }

        /* Mobile Menu */
        .mobile-menu-btn {
            display: none;
            background: transparent;
            border: none;
            color: var(--text-primary);
            font-size: 1.5rem;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .site-nav {
                display: none;
            }

            .mobile-menu-btn {
                display: block;
            }

            .site-navbar-actions .btn-nav-outline {
                display: none;
            }
        }

        /* ===== GUEST PAGE STYLES ===== */
        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .section {
            padding: 6rem 0;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.95rem;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
        }

        .btn-outline {
            background: transparent;
            border: 2px solid var(--border-color);
            color: var(--text-primary);
        }

        .btn-outline:hover {
            border-color: var(--bs-primary);
            color: var(--bs-primary);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--bs-primary) 0%, var(--bs-primary) 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4);
        }

        .btn-lg {
            padding: 1rem 2rem;
            font-size: 1.1rem;
        }

        /* ===== FOOTER ===== */
        .footer {
            background: var(--bg-white);
            border-top: 1px solid var(--border-color);
            padding: 3rem 0;
        }

        .footer-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 2rem;
        }

        .footer-brand {
            max-width: 300px;
        }

        .footer-brand p {
            color: var(--text-secondary);
            margin-top: 1rem;
            font-size: 0.95rem;
        }

        .footer-links {
            display: flex;
            gap: 4rem;
        }

        .footer-column h4 {
            color: var(--text-primary);
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .footer-column ul {
            list-style: none;
        }

        .footer-column li {
            margin-bottom: 0.75rem;
        }

        .footer-column a {
            color: var(--text-secondary);
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-column a:hover {
            color: var(--bs-primary);
        }

        .footer-bottom {
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .footer-bottom p {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .footer-social {
            display: flex;
            gap: 1rem;
        }

        .footer-social a {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--bg-light);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-secondary);
            text-decoration: none;
            transition: all 0.3s;
        }

        .footer-social a:hover {
            background: var(--bs-primary);
            color: white;
        }

        @media (max-width: 768px) {
            .footer-container {
                flex-direction: column;
            }

            .footer-links {
                flex-direction: column;
                gap: 2rem;
            }

            .footer-bottom {
                flex-direction: column;
                text-align: center;
            }
        }

        /* ===== AUTH PAGE STYLES ===== */
        .auth-section {
            min-height: calc(100vh - 73px);
            margin-top: 73px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 2rem;
            position: relative;
            overflow: hidden;
        }

        /* Animated background shapes */
        .bg-shapes {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }

        .bg-shapes .shape {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: float 15s infinite ease-in-out;
        }

        .bg-shapes .shape:nth-child(1) {
            width: 400px;
            height: 400px;
            top: -100px;
            left: -100px;
            animation-delay: 0s;
        }

        .bg-shapes .shape:nth-child(2) {
            width: 300px;
            height: 300px;
            bottom: -50px;
            right: -50px;
            animation-delay: -5s;
        }

        .bg-shapes .shape:nth-child(3) {
            width: 200px;
            height: 200px;
            top: 50%;
            left: 50%;
            animation-delay: -10s;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0) rotate(0deg);
            }
            33% {
                transform: translateY(-30px) rotate(10deg);
            }
            66% {
                transform: translateY(20px) rotate(-5deg);
            }
        }

        .login-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 450px;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            padding: 3rem;
            border: 1px solid rgba(255, 255, 255, 0.3);
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .brand-logo {
            text-align: center;
            margin-bottom: 2rem;
        }

        .brand-logo .icon-wrapper {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--bs-primary) 0%, var(--bs-primary) 100%);
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            box-shadow: 0 10px 30px rgba(99, 102, 241, 0.4);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                box-shadow: 0 10px 30px rgba(99, 102, 241, 0.4);
            }
            50% {
                box-shadow: 0 10px 40px rgba(99, 102, 241, 0.6);
            }
        }

        .brand-logo .icon-wrapper i {
            font-size: 2.5rem;
            color: white;
        }

        .brand-logo h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .brand-logo p {
            color: var(--text-secondary);
            font-size: 0.95rem;
        }

        .form-floating {
            margin-bottom: 1.25rem;
        }

        .form-floating .form-control {
            height: 60px;
            border: 2px solid var(--border-color);
            border-radius: 12px;
            padding: 1rem 1rem 1rem 3rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f8fafc;
        }

        .form-floating .form-control:focus {
            border-color: var(--bs-primary);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
            background: white;
        }

        .form-floating label {
            padding: 1rem 1rem 1rem 3rem;
            color: var(--text-muted);
        }

        .form-floating .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 1.1rem;
            z-index: 5;
            transition: color 0.3s ease;
        }

        .form-floating:focus-within .input-icon {
            color: var(--bs-primary);
        }

        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            z-index: 5;
            padding: 0.5rem;
            transition: color 0.3s ease;
        }

        .password-toggle:hover {
            color: var(--bs-primary);
        }

        .form-check-input:checked {
            background-color: var(--bs-primary);
            border-color: var(--bs-primary);
        }

        .form-check-input:focus {
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
            border-color: var(--bs-primary);
        }

        .form-check-label {
            color: var(--text-secondary);
            cursor: pointer;
        }

        .forgot-password {
            color: var(--bs-primary);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .forgot-password:hover {
            color: var(--bs-primary);
            text-decoration: underline;
        }

        .btn-login {
            width: 100%;
            height: 56px;
            background: linear-gradient(135deg, var(--bs-primary) 0%, var(--bs-primary) 100%);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(99, 102, 241, 0.4);
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-login:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .btn-login i {
            margin-left: 0.5rem;
            transition: transform 0.3s ease;
        }

        .btn-login:hover i {
            transform: translateX(5px);
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 1.5rem 0;
            color: var(--text-muted);
            font-size: 0.875rem;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border-color);
        }

        .divider span {
            padding: 0 1rem;
        }

        .social-login {
            display: flex;
            gap: 1rem;
        }

        .btn-social {
            flex: 1;
            height: 50px;
            border: 2px solid var(--border-color);
            border-radius: 12px;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-weight: 500;
            color: var(--text-primary);
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-social:hover {
            border-color: var(--bs-primary);
            background: #f8fafc;
            transform: translateY(-2px);
        }

        .btn-social i {
            font-size: 1.25rem;
        }

        .btn-social.google i {
            color: #ea4335;
        }

        .btn-social.github i {
            color: #333;
        }

        .signup-link {
            text-align: center;
            margin-top: 2rem;
            color: var(--text-secondary);
        }

        .signup-link a {
            color: var(--bs-primary);
            font-weight: 600;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .signup-link a:hover {
            color: var(--bs-primary);
            text-decoration: underline;
        }

        /* Auth responsive adjustments */
        @media (max-width: 576px) {
            .login-card {
                padding: 2rem 1.5rem;
            }

            .brand-logo .icon-wrapper {
                width: 64px;
                height: 64px;
            }

            .brand-logo .icon-wrapper i {
                font-size: 2rem;
            }

            .brand-logo h1 {
                font-size: 1.5rem;
            }

            .social-login {
                flex-direction: column;
            }
        }

        /* Input autofill styling */
        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus {
            -webkit-box-shadow: 0 0 0px 1000px #f8fafc inset;
            -webkit-text-fill-color: var(--text-primary);
            transition: background-color 5000s ease-in-out 0s;
        }
    </style>
    {{ $styles ?? '' }}
    @stack('styles')
</head>

<body>
    {{-- Navbar (shared across all pages) --}}
    <nav class="site-navbar">
        <div class="site-navbar-container">
            <a href="{{ route('home') }}" class="site-brand">
                <i class="fas fa-car-side"></i>
                <span>RodaTa</span>
            </a>

            <ul class="site-nav">
                <li><a href="/#stok" class="site-nav-link">Stok Mobil</a></li>
                <li><a href="/#keunggulan" class="site-nav-link">Kenapa Kami</a></li>
                <li><a href="/#proses" class="site-nav-link">Cara Beli</a></li>
                <li><a href="/#kontak" class="site-nav-link">Kontak</a></li>
            </ul>

            <div class="site-navbar-actions">
                <button class="theme-toggle" onclick="toggleTheme()">
                    <i class="fas fa-moon" id="theme-icon"></i>
                </button>
                <a href="tel:081234567890" class="btn-nav btn-nav-outline"><i class="fas fa-phone"></i> Hubungi Kami</a>
                <button class="mobile-menu-btn">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </nav>

    @if($type === 'auth')
        {{-- Auth Pages: gradient section with animated shapes --}}
        <section class="auth-section">
            <div class="bg-shapes">
                <div class="shape"></div>
                <div class="shape"></div>
                <div class="shape"></div>
            </div>

            {{ $slot }}
        </section>
    @else
        {{-- Guest Pages: main content + footer --}}
        <main>
            {{ $slot }}
        </main>

        <footer class="footer">
            <div class="container">
                <div class="footer-container">
                    <div class="footer-brand">
                         <a href="{{ route('home') }}" class="site-brand">
                            <i class="fas fa-car-side"></i>
                            <span>RodaTa</span>
                        </a>
                         <p>Teman perjalanan Anda menemukan mobil bekas berkualitas, jujur, dan siap jalan.</p>
                    </div>

                    <div class="footer-links">
                        <div class="footer-column">
                            <h4>Jelajahi</h4>
                            <ul>
                                 <li><a href="#stok">Stok Mobil</a></li>
                                 <li><a href="#keunggulan">Keunggulan</a></li>
                                 <li><a href="#proses">Cara Beli</a></li>
                                 <li><a href="#kontak">Kontak</a></li>
                            </ul>
                        </div>
                        <div class="footer-column">
                            <h4>Layanan</h4>
                            <ul>
                                 <li><a href="#">Inspeksi unit</a></li>
                                 <li><a href="#">Test drive</a></li>
                                 <li><a href="#">Tukar tambah</a></li>
                                 <li><a href="#">Bantuan</a></li>
                            </ul>
                        </div>
                        <div class="footer-column">
                            <h4>Legal</h4>
                            <ul>
                                 <li><a href="#">Tentang Kami</a></li>
                                 <li><a href="#">Kebijakan Privasi</a></li>
                                 <li><a href="#">Syarat & Ketentuan</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="footer-bottom">
                    <p>&copy; {{ date('Y') }} RodaTa. Semua hak dilindungi.</p>
                    <div class="footer-social">
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-github"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
        </footer>
    @endif

    <script src="{{ asset('assets/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        // Theme Toggle
        function initTheme() {
            const savedTheme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

            if (savedTheme) {
                document.documentElement.setAttribute('data-theme', savedTheme);
            } else if (prefersDark) {
                document.documentElement.setAttribute('data-theme', 'dark');
            }

            updateThemeIcon();
        }

        function toggleTheme() {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateThemeIcon();
        }

        function updateThemeIcon() {
            const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
            const themeIcon = document.getElementById('theme-icon');
            if (themeIcon) {
                themeIcon.className = isDark ? 'fas fa-sun' : 'fas fa-moon';
            }
        }

        initTheme();

        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
    {{ $scripts ?? '' }}
    @stack('scripts')
</body>

</html>
