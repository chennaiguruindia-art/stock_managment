<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login &mdash; {{ config('invoice.company.name', 'ZYRA BILLING') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --primary: #1554d1;
            --primary-hover: #1043a8;
            --primary-soft: #e7eefe;
            --dark: #0e1726;
            --muted: #66748b;
            --border: #dfe4ee;
            --surface: #ffffff;
            --bg: #f4f6fb;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background: var(--bg);
            color: var(--dark);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            margin: 0;
            background-image: 
                radial-gradient(circle at 15% 15%, rgba(21, 84, 209, 0.08) 0%, transparent 40%),
                radial-gradient(circle at 85% 85%, rgba(15, 157, 88, 0.06) 0%, transparent 40%);
        }

        .login-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(16, 24, 40, 0.08);
            width: 100%;
            max-width: 440px;
            padding: 2.25rem 2.25rem 2rem;
            position: relative;
            overflow: hidden;
        }

        .login-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary) 0%, #38bdf8 100%);
        }

        .brand-logo-wrap {
            display: flex;
            align-items: center;
            gap: .75rem;
            margin-bottom: 1.5rem;
        }

        .brand-badge {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--primary), #3b82f6);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.35rem;
            font-weight: 800;
            box-shadow: 0 4px 12px rgba(21, 84, 209, 0.25);
        }

        .brand-info .brand-name {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--dark);
            line-height: 1.1;
            letter-spacing: -0.02em;
        }

        .brand-info .brand-sub {
            font-size: .75rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: var(--muted);
            font-weight: 700;
        }

        .login-title {
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--dark);
            margin-bottom: .3rem;
            letter-spacing: -0.01em;
        }

        .login-desc {
            font-size: .84rem;
            color: var(--muted);
            margin-bottom: 1.5rem;
            line-height: 1.45;
        }

        .form-label {
            font-size: .82rem;
            font-weight: 700;
            color: #334155;
            margin-bottom: .4rem;
        }

        .input-group-custom {
            position: relative;
            margin-bottom: 1.15rem;
        }

        .input-group-custom i.icon-lead {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1rem;
            pointer-events: none;
            z-index: 5;
        }

        .input-group-custom .form-control {
            padding: .7rem .85rem .7rem 2.6rem;
            font-size: .92rem;
            border-radius: 10px;
            border: 1px solid var(--border);
            color: var(--dark);
            font-weight: 500;
            transition: all .2s ease;
        }

        .input-group-custom .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(21, 84, 209, 0.15);
            outline: none;
        }

        .toggle-pwd {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 4px;
            font-size: 1rem;
            z-index: 5;
        }

        .toggle-pwd:hover {
            color: var(--dark);
        }

        .btn-login {
            background: var(--primary);
            color: #ffffff;
            border: none;
            border-radius: 10px;
            padding: .75rem;
            font-size: .95rem;
            font-weight: 700;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            transition: all .2s ease;
            box-shadow: 0 4px 12px rgba(21, 84, 209, 0.25);
            margin-top: .5rem;
        }

        .btn-login:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(21, 84, 209, 0.32);
            color: #ffffff;
        }

        .login-footer {
            margin-top: 1.5rem;
            padding-top: 1.25rem;
            border-top: 1px solid #edf2f7;
            text-align: center;
            font-size: .78rem;
            color: var(--muted);
        }

        .alert {
            border-radius: 10px;
            font-size: .84rem;
            padding: .7rem .9rem;
            margin-bottom: 1.25rem;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <!-- Brand Header -->
        <div class="brand-logo-wrap">
            <div class="brand-badge">
                <i class="bi bi-box-seam"></i>
            </div>
            <div class="brand-info">
                <div class="brand-name">{{ config('invoice.company.name', 'ZYRA') }}</div>
                <div class="brand-sub">Billing &amp; Stock Suite</div>
            </div>
        </div>

        <div class="login-title">Sign In</div>
        <div class="login-desc">Enter your credentials to access your billing terminal and stock management.</div>

        <!-- Session Status / Errors -->
        @if (session('success'))
            <div class="alert alert-success d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <div>{{ $errors->first() }}</div>
            </div>
        @endif

        <form method="POST" action="{{ route('login_post') }}" autocomplete="off">
            @csrf

            <!-- Login Input (Email or Username) -->
            <div class="mb-3">
                <label for="login" class="form-label">Email or Username</label>
                <div class="input-group-custom">
                    <i class="bi bi-person icon-lead"></i>
                    <input 
                        type="text" 
                        class="form-control @error('login') is-invalid @enderror" 
                        id="login" 
                        name="login" 
                        value="{{ old('login') }}" 
                        placeholder="e.g. admin or test@example.com" 
                        required 
                        autofocus
                    >
                </div>
            </div>

            <!-- Password Input -->
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="input-group-custom">
                    <i class="bi bi-lock icon-lead"></i>
                    <input 
                        type="password" 
                        class="form-control @error('password') is-invalid @enderror" 
                        id="password" 
                        name="password" 
                        placeholder="Enter your password" 
                        required
                    >
                    <button type="button" class="toggle-pwd" id="togglePwdBtn" tabindex="-1" aria-label="Toggle password">
                        <i class="bi bi-eye" id="togglePwdIcon"></i>
                    </button>
                </div>
            </div>

            <!-- Remember Me -->
            <div class="d-flex align-items-center justify-content-between mb-3" style="font-size: .84rem;">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label text-muted" for="remember">
                        Remember me
                    </label>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-login">
                <span>Sign In</span>
                <i class="bi bi-arrow-right"></i>
            </button>
        </form>

        <div class="login-footer">
            <i class="bi bi-shield-lock me-1"></i> Secure terminal access &middot; {{ date('Y') }}
        </div>
    </div>

    <script>
        const toggleBtn = document.getElementById('togglePwdBtn');
        const pwdInput = document.getElementById('password');
        const pwdIcon = document.getElementById('togglePwdIcon');

        if (toggleBtn && pwdInput) {
            toggleBtn.addEventListener('click', function () {
                const isPassword = pwdInput.type === 'password';
                pwdInput.type = isPassword ? 'text' : 'password';
                pwdIcon.className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
            });
        }
    </script>
</body>
</html>
