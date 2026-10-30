<x-layouts.guest title="Login - RodaTa" description="Masuk ke panel admin RodaTa" type="auth">
    <div class="login-container">
        <div class="login-card">
            <!-- Brand Logo -->
            <div class="brand-logo">
                <div class="icon-wrapper">
                    <i class="fas fa-car-side"></i>
                </div>
                <h1>Selamat datang kembali</h1>
                <p>Masuk ke panel admin RodaTa</p>
            </div>

            <!-- Login Form -->
            <form method="POST" action="{{ route('login.store') }}" id="login-form">
                @csrf

                <!-- Email Field -->
                <div class="form-floating position-relative">
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="form-control @error('email') is-invalid @enderror" id="email"
                        placeholder="Email" autocomplete="email" required autofocus>
                    <label for="email">Email</label>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password Field -->
                <div class="form-floating position-relative">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" name="password"
                        class="form-control @error('password') is-invalid @enderror" id="password"
                        placeholder="Kata sandi" autocomplete="current-password" required>
                    <label for="password">Kata sandi</label>
                    <button type="button" class="password-toggle" onclick="togglePassword()">
                        <i class="fas fa-eye" id="toggleIcon"></i>
                    </button>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check mb-0">
                        <input class="form-check-input" type="checkbox" name="remember" value="1"
                            id="remember" @checked(old('remember'))>
                    <label class="form-check-label" for="remember">Ingat saya</label>
                </div>
                    <a href="#" class="forgot-password">Lupa kata sandi?</a>
                </div>

                <!-- Login Button -->
                <button type="submit" class="btn btn-login" id="login-button">
                    <span id="login-text">Masuk <i class="fas fa-arrow-right"></i></span>
                    <span id="login-loading" class="d-none">
                        <i class="fas fa-spinner fa-spin me-2"></i> Memproses...
                    </span>
                </button>
            </form>

            <!-- Divider -->
            <div class="divider">
                <span>akses khusus admin</span>
            </div>

            <!-- Sign Up Link -->
            <div class="signup-link">
                Belum punya akun? <a href="{{ route('register') }}">Buat akun</a>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function togglePassword() {
                const input = document.getElementById('password');
                const icon = document.getElementById('toggleIcon');

                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.replace('fa-eye', 'fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.replace('fa-eye-slash', 'fa-eye');
                }
            }

            document.getElementById('login-form').addEventListener('submit', function () {
                document.getElementById('login-button').disabled = true;
                document.getElementById('login-text').classList.add('d-none');
                document.getElementById('login-loading').classList.remove('d-none');
            });
        </script>
    @endpush
</x-layouts.guest>
