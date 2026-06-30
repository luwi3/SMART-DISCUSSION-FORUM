<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Smart Discussion Forum</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', system-ui, sans-serif; }
        body { background-color: #f0f7ff; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 40px 20px; }
        
        /* Main Layout Card Grid Wrapper */
        .register-card { background: white; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); border: 1px solid #eef2f6; padding: 40px; width: 100%; max-width: 840px; }
        
        /* Headers */
        .header { text-align: center; margin-bottom: 30px; }
        .logo-flex { display: flex; align-items: center; justify-content: center; gap: 8px; }
        .logo-icon { width: 34px; height: 34px; color: #2563eb; }
        .logo-text { font-size: 19px; font-weight: 900; color: #111827; }
        .logo-blue { color: #2563eb; }
        .title { font-size: 26px; font-weight: 700; color: #0f172a; margin-top: 12px; }
        .subtitle { font-size: 13px; color: #64748b; margin-top: 4px; }
        
        /* Dual Grid Column Structural System */
        .split-layout { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; align-items: start; }
        @media (max-width: 768px) { .split-layout { grid-template-columns: 1fr; } }
        
        /* Field Layout Wrapper Inputs */
        .form-group { margin-bottom: 14px; }
        .label { display: block; font-size: 11px; font-weight: 700; color: #1e293b; text-transform: uppercase; margin-bottom: 5px; }
        .input-wrapper { position: relative; display: flex; align-items: center; }
        .input-icon { position: absolute; left: 12px; width: 16px; height: 16px; color: #94a3b8; }
        .input-field { width: 100%; padding: 10px 12px 10px 38px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13.5px; color: #0f172a; transition: all 0.2s; background: white; }
        .input-field:focus { outline: none; border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15); }
        .select-field { appearance: none; cursor: pointer; color: #475569; }
        .select-arrow { position: absolute; right: 12px; width: 14px; height: 14px; color: #94a3b8; pointer-events: none; }
        
        /* Right Hand Rule Sheet Panel Container */
        .rules-sheet { background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 22px; }
        .rules-title { display: flex; align-items: center; gap: 8px; color: #1d4ed8; font-weight: 700; font-size: 14px; margin-bottom: 14px; }
        .rules-list { list-style: none; display: flex; flex-direction: column; gap: 12px; font-size: 12.5px; color: #334155; font-weight: 500; }
        .checkbox-box { border-top: 1px solid #e2e8f0; margin-top: 20px; padding-top: 16px; display: flex; align-items: flex-start; gap: 8px; font-size: 12.5px; color: #1e293b; font-weight: 600; cursor: pointer; }
        .checkbox-box input { width: 15px; height: 15px; margin-top: 2px; border-radius: 4px; border: 1px solid #cbd5e1; }
        
        /* Action Buttons Grid Footer Row */
        .footer-action-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 30px; padding-top: 20px; border-top: 1px solid #f1f5f9; }
        .btn-decline { display: flex; align-items: center; justify-content: center; gap: 6px; border: 1px solid #f43f5e; background: transparent; color: #e11d48; text-decoration: none; padding: 12px; font-weight: 700; font-size: 13.5px; border-radius: 8px; transition: background 0.2s; }
        .btn-decline:hover { background: #fff5f5; }
        .btn-agree { display: flex; align-items: center; justify-content: center; gap: 6px; background: #10b981; border: none; color: white; padding: 12px; font-weight: 700; font-size: 13.5px; border-radius: 8px; cursor: pointer; transition: background 0.2s; }
        .btn-agree:hover { background: #059669; }
        
        .footer-login-nav { text-align: center; margin-top: 20px; font-size: 12.5px; color: #64748b; font-weight: 500; }
        .footer-login-nav a { color: #2563eb; font-weight: 700; text-decoration: none; margin-left: 4px; }
        .footer-login-nav a:hover { text-decoration: underline; }
    </style>
</head>
<body>

    <div class="register-card">
        <div class="header">
            <div class="logo-flex">
                <svg class="logo-icon" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM3.52 10.36L12 14.99l8.48-4.63L12 5.74 3.52 10.36zM12 17.5c-2.43 0-4.63-.96-6.26-2.5l-1.42 1.42C6.46 18.54 9.07 19.5 12 19.5s5.54-.96 7.68-2.58l-1.42-1.42c-1.63 1.54-3.83 2.5-6.26 2.5z"/>
                </svg>
                <span class="logo-text">SMART <span class="logo-blue">DISCUSSION</span> FORUM</span>
            </div>
            <h2 class="title">Create New Account</h2>
            <p class="subtitle">Fill in your details to get started</p>
        </div>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="split-layout">
                
                <div>
                    <div class="form-group">
                        <label for="name" class="label">Full Name</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus placeholder="Enter your full name" class="input-field">
                        </div>
                        <x-input-error :messages="$errors->get('name')" />
                    </div>

                    <div class="form-group">
                        <label for="email" class="label">Email Address</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required placeholder="Enter your email address" class="input-field">
                        </div>
                        <x-input-error :messages="$errors->get('email')" />
                    </div>

                    <div class="form-group">
                        <label for="contact" class="label">Contact</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.72.73.73 0 00.58.45l3.77 1.25a.73.73 0 00.58-.45 1 1 0 01.94-.72H19a2 2 0 012 2v1.5a12.06 12.06 0 01-12 12H5a2 2 0 01-2-2V5z"/></svg>
                            <input id="contact" type="text" name="contact" value="{{ old('contact') }}" required placeholder="Enter your contact number" class="input-field">
                        </div>
                        <x-input-error :messages="$errors->get('contact')" />
                    </div>

                    <div class="form-group">
                        <label for="password" class="label">Password</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            <input id="password" type="password" name="password" required placeholder="Create a password" class="input-field">
                            <svg class="password-eye" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </div>
                        <x-input-error :messages="$errors->get('password')" />
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation" class="label">Confirm Password</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            <input id="password_confirmation" type="password" name="password_confirmation" required placeholder="Confirm your password" class="input-field">
                        </div>
                        <x-input-error :messages="$errors->get('password_confirmation')" />
                    </div>

                    <div class="form-group">
                        <label for="role" class="label">Select Role</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <select id="role" name="role" required class="input-field select-field">
                                <option value="" disabled selected>-- Select Role --</option>
                                <option value="Student">Student</option>
                                <option value="Lecturer">Lecturer</option>
                                <option value="Administrator">Administrator</option>
                            </select>
                            <svg class="select-arrow" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                        <x-input-error :messages="$errors->get('role')" />
                    </div>
                </div>

                <div class="rules-sheet">
                    <div class="rules-title">
                        <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        Forum Rules & Regulations
                    </div>
                    <ul class="rules-list">
                        <li>1. Respect all members</li>
                        <li>2. No abusive or offensive language</li>
                        <li>3. Participate actively</li>
                        <li>4. Keep discussions relevant</li>
                        <li>5. Do not spam or post irrelevant content</li>
                    </ul>

                    <label class="checkbox-box">
                        <input type="checkbox" name="rules_agreed" required>
                        <span>I have read and understood the rules</span>
                    </label>
                    <x-input-error :messages="$errors->get('rules_agreed')" />
                </div>

            </div>

            <div class="footer-action-grid">
                <a href="{{ route('login') }}" class="btn-decline">
                    <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    DECLINE
                </a>
                <button type="submit" class="btn-agree">
                    <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    AGREE & CONTINUE
                </button>
            </div>
        </form>

        <div class="footer-login-nav">
            Already have an account? <a href="{{ route('login') }}">Login</a>
        </div>
    </div>

</body>
</html>