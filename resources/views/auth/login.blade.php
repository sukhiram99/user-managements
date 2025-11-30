{{-- resources/views/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - User Management Portal</title>

    <!-- Bootstrap 5 + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            border-radius: 1rem;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .login-header {
            background: #5a67d8;
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .login-body {
            padding: 2.5rem;
            background: white;
        }

        .btn-login {
            background: #5a67d8;
            border: none;
            padding: 0.75rem;
            font-weight: 600;
        }

        .btn-login:hover {
            background: #4c51bf;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">

                <div class="login-card">
                    <!-- Header -->
                    <div class="login-header">
                        <h3><i class="bi bi-shield-lock"></i> User Management Portal</h3>
                        <p class="mb-0">Sign in to continue</p>
                    </div>
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    <!-- Form -->
                    <div class="login-body">

                        <form method="POST" action="{{ route('loginSubmit') }}">
                            @csrf

                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" name="email" id="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        value="{{ old('email') ?? 'admin@example.com' }}" placeholder="Enter Email.."
                                        required autofocus>
                                </div>
                                @error('email')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input type="password" name="password" id="password" placeholder="Enter Password.."
                                        class="form-control @error('password') is-invalid @enderror" required
                                        autocomplete="current-password">
                                </div>
                                @error('password')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Remember Me -->
                            <div class="mb-3 form-check">
                                <input type="checkbox" name="remember" id="remember" class="form-check-input">
                                <label class="form-check-label" for="remember">Remember me</label>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary btn-login w-100">
                                <i class="bi bi-box-arrow-in-right"></i> Login
                            </button>

                            <!-- General Error (wrong credentials) -->
                            @if ($errors->has('email') && $errors->first('email') === 'These credentials do not match our records.')
                                <div class="alert alert-danger mt-3 small">
                                    Invalid email or password.
                                </div>
                            @endif

                            <!-- Links -->
                            <div class="text-center mt-4">
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="text-decoration-none small">
                                        Forgot your password?
                                    </a>
                                @endif
                            </div>

                            <!-- Quick Admin Login Hint (only in development) -->
                            {{-- @if (config('app.env') === 'local')
                                <div class="alert alert-info mt-3 small text-center">
                                    <strong>Dev Login:</strong><br>
                                    Email: admin@example.com<br>
                                    Pass: admin123
                                </div>
                            @endif --}}

                        </form>
                    </div>
                </div>

                <div class="text-center text-white mt-3">
                    <small>© {{ date('Y') }} User Management Portal. All rights reserved.</small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
