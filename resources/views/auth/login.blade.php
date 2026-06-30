<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Smart Discussion Forum</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', system-ui, sans-serif; }
        body { background-color: #f0f7ff; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        
        /* Main Login Card */
        .card { background: #ffffff; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); border: 1px solid #eef2f6; padding: 40px; w-full; max-width: 480px; width: 100%; }
        
        /* Header Logo Styling */
        .logo-area { text-align: center; margin-bottom: 24px; }
        .logo-flex { display: flex; align-items: center; justify-content: center; gap: 8px; }
        .logo-icon { width: 32px; height: 32px; color: #1d4ed8; }
        .logo-text { font-size: 18px; font-weight: 900; tracking: 0.5px; color: #111827; }
        .logo-blue { color: #2563eb; }
        .title { font-size: 26px; font-weight: 700; color: #0f172a; margin-top: 16px; }
        .subtitle { font-size: 13px; color: #64748b; margin-top: 4px; }
        
        /* Form Groups and Inputs with Icons */
        .form-group { margin-bottom: 18px; }
        .label { display: block; font-size: 11px; font-weight: 700; color: #1e293b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; }
        .input-wrapper { position: relative; display: flex; align-items: center; }
        .input-icon { position: absolute; left: 14px; width: 18px; height: 18px; color: #94a3b8; }
        .input-field { width: 100%; padding: 12px 14px 12px 42px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; color: #0f172a; transition: all 0.2s; }
        .input-field:focus { outline: none; border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15); }
        .password-eye { position: absolute; right: 14px; width: 18px; height: 18px; color: #94a3b8; cursor: pointer; }
        
        /* Options row */
        .options-row { display: flex; align-items: center; justify-content: space-between; font-size: 13px; margin: 12px 0 20px 0; }
        .remember-me { display: flex; align-items: center; gap: 6px; color: #475569; cursor: pointer; }
        .forgot-link { color: #2563eb; text-decoration: none; font-weight: 600; }
        .forgot-link:hover { text-decoration: underline; }
        
        /* Buttons */
        .btn-blue { width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; background: #2563eb; color: white; border: none; padding: 12px; border-radius: 8px; font-size: 14px; font-weight: 700; cursor: pointer; transition: background 0.2s; }
        .btn-blue:hover { background: #1d4ed8; }
        .divider { display: flex; align-items: center; text-align: center; margin: 24px 0; color: #94a3b8; font-size: 12px; text-transform: uppercase; }
        .divider::before, .divider::after { content: ''; flex: 1; border-bottom: 1px solid #e2e8f0; }
        .divider:not(:empty)::before { margin-right: 12px; }
        .divider:not(:empty)::after { margin-left: 12px; }
        
        .btn-register-outline { width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; border: 2px solid #10b981; background: transparent; color: #059669; padding: 11px; border-radius: 8px; font-size: 14px; font-weight: 700; text-decoration: none; transition: background 0.2s; }
        .btn-register-outline:hover { background: #f0fdf4; }
        .center-footer { text-align: center; margin-top: 16px; font-size: 13px; color: #64748b; }
    </style>
</head>
<body>

    <div class="card">
        <div class="logo-area">
            <div class="logo-flex">
                <svg class="logo-icon" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM3.52 10.36L12 14.99l8.48-4.63L12 5.74 3.52 10.36zM12 17.5c-2.43 0-4.63-.96-6.26-2.5l-1.42 1.42C6.46 18.54 9.07 19.5 12 19.5s5.54-.96 7.68-2.58l-1.42-1.42c-1.63 1.54-3.83 2.5-6.26 2.5z"/>
                </svg>
                <span class="logo-text">SMART <span class="logo-blue">DISCUSSION</span> FORUM</span>
            </div>
            <h2 class="title">Welcome Back!</h2>
            <p class="subtitle">Login to continue to your account</p>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="email" class="label">Email / Username</label>
                <div class="input-wrapper">
                    <svg class="input-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="Enter your email or username" class="input-field">
                </div>
                <x-input-error :messages="$errors->get('email')" />
            </div>

            <div class="form-group">
                <label for="password" class="label">Password</label>
                <div class="input-wrapper">
                    <svg class="input-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <input id="password" type="password" name="password" required placeholder="Enter your password" class="input-field">
                    <svg class="password-eye" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </div>
                <x-input-error :messages="$errors->get('password')" />
            </div>

            <div class="options-row">
                <label class="remember-me">
                    <input type="checkbox" name="remember"> Remember me
                </label>
                @if (Route::has('password.request'))
                    <a class="forgot-link" href="{{ route('password.request') }}">Forgot Password?</a>
                @endif
            </div>

            <button type="submit" class="btn-blue">
                <svg style="width:16px;height:16px;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
                LOGIN
            </button>
        </form>

        <div class="divider">or</div>

        <div style="text-align: center;">
            <p style="font-size: 13px; color: #64748b; margin-bottom: 12px;">Don't have an account?</p>
            <a href="{{ route('register') }}" class="btn-register-outline">
                <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                REGISTER
            </a>
        </div>
    </div>

</body>
</html>