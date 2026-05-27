<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Flow — Reset Password</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Inter',sans-serif;min-height:100vh;display:flex;background:#F0F4F8}
        .left-panel{flex:1;background:linear-gradient(160deg,#1E3A5F 0%,#2563EB 60%,#1D4ED8 100%);display:flex;flex-direction:column;justify-content:space-between;padding:52px 56px;position:relative;overflow:hidden}
        .circle-decor{position:absolute;width:400px;height:400px;border-radius:50%;border:1px solid rgba(255,255,255,0.07);bottom:-100px;right:-100px}
        .left-logo{display:flex;align-items:center;gap:12px;position:relative;z-index:2}
        .logo-icon{width:46px;height:46px;background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.25);border-radius:12px;display:flex;align-items:center;justify-content:center}
        .logo-icon span{font-size:18px;font-weight:800;color:white}
        .logo-txt{font-size:18px;font-weight:700;color:white}
        .left-body{position:relative;z-index:2}
        .left-body h2{font-size:34px;font-weight:800;color:white;line-height:1.2;letter-spacing:-1px;margin-bottom:16px}
        .left-body h2 span{color:#93C5FD}
        .left-body p{font-size:14px;color:rgba(255,255,255,0.65);line-height:1.7;max-width:320px;margin-bottom:28px}
        .tip-card{background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.12);border-radius:14px;padding:18px 20px;margin-top:8px}
        .tip-card strong{display:block;font-size:13px;font-weight:700;color:white;margin-bottom:8px}
        .tip-item{display:flex;align-items:center;gap:8px;margin-bottom:7px}
        .tip-item:last-child{margin-bottom:0}
        .tip-dot{width:6px;height:6px;background:#93C5FD;border-radius:50%;flex-shrink:0}
        .tip-item span{font-size:12px;color:rgba(255,255,255,0.6)}
        .left-foot{font-size:12px;color:rgba(255,255,255,0.35);position:relative;z-index:2}
        .right-panel{width:480px;background:#fff;display:flex;flex-direction:column;justify-content:center;padding:60px 52px;box-shadow:-8px 0 40px rgba(0,0,0,0.08)}
        .icon-box{width:60px;height:60px;background:#EFF6FF;border-radius:16px;display:flex;align-items:center;justify-content:center;margin-bottom:22px}
        .icon-box svg{width:28px;height:28px;color:#2563EB}
        .fh h1{font-size:24px;font-weight:800;color:#0F172A;margin-bottom:6px}
        .fh p{font-size:14px;color:#64748B;line-height:1.6;margin-bottom:28px}
        .field{margin-bottom:18px}
        .field label{display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:7px}
        .iw{position:relative}
        .iw svg{position:absolute;left:13px;top:50%;transform:translateY(-50%);width:16px;height:16px;color:#9CA3AF;pointer-events:none;transition:color .2s}
        .iw input{width:100%;height:48px;padding:0 16px 0 40px;background:#F8FAFC;border:1.5px solid #E2E8F0;border-radius:10px;font-size:14px;color:#1E293B;font-family:'Inter',sans-serif;font-weight:500;outline:none;transition:all .2s}
        .iw input::placeholder{color:#CBD5E1;font-weight:400}
        .iw input:focus{border-color:#2563EB;background:#EFF6FF;box-shadow:0 0 0 4px rgba(37,99,235,.08)}
        .iw input:focus~svg{color:#2563EB}
        .err-box{background:#FEF2F2;border:1.5px solid #FCA5A5;border-radius:10px;padding:11px 14px;margin-bottom:16px}
        .err-box p{font-size:12px;color:#DC2626;font-weight:500}
        .btn{width:100%;height:50px;background:#2563EB;border:none;border-radius:10px;color:white;font-family:'Inter',sans-serif;font-size:14px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:all .2s;box-shadow:0 4px 12px rgba(37,99,235,.25);margin-top:8px}
        .btn:hover{background:#1D4ED8;transform:translateY(-1px);box-shadow:0 6px 20px rgba(37,99,235,.35)}
        .back-link{display:flex;align-items:center;gap:6px;margin-top:20px;font-size:13px;color:#64748B;text-decoration:none;transition:color .2s}
        .back-link:hover{color:#2563EB}
        .back-link svg{width:15px;height:15px}
        @media(max-width:768px){.left-panel{display:none}.right-panel{width:100%;padding:40px 28px}}
    </style>
</head>
<body>
<div class="left-panel">
    <div class="circle-decor"></div>
    <div class="left-logo">
        <div class="logo-icon">
            <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2L2 7l10 5 10-5-10-5z" />
                <path d="M2 17l10 5 10-5" />
                <path d="M2 12l10 5 10-5" />
                <path d="M12 22V12" stroke="#60A5FA" stroke-width="3" />
                <path d="M12 12l4-4" stroke="#60A5FA" stroke-width="3" />
            </svg>
        </div>
        <span class="logo-txt">Stock Flow</span>
    </div>
    <div class="left-body">
        <h2>Create a<br><span>Strong</span><br>New Password</h2>
        <p>You're almost there. Set a new password to regain full access to your business workspace.</p>
        <div class="tip-card">
            <strong>🔒 Password Tips</strong>
            <div class="tip-item"><div class="tip-dot"></div><span>At least 8 characters long</span></div>
            <div class="tip-item"><div class="tip-dot"></div><span>Mix uppercase and lowercase letters</span></div>
            <div class="tip-item"><div class="tip-dot"></div><span>Include numbers and symbols</span></div>
            <div class="tip-item"><div class="tip-dot"></div><span>Avoid using your name or username</span></div>
        </div>
    </div>
    <div class="left-foot">&copy; {{ date('Y') }} Stock Flow Business Manager</div>
</div>

<div class="right-panel">
    <div class="icon-box">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
    </div>
    <div class="fh">
        <h1>Set new password</h1>
        <p>Choose a strong, unique password for your account. Make sure to save it somewhere safe.</p>
    </div>

    @if($errors->any())
    <div class="err-box">@foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach</div>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="field">
            <label>Email Address</label>
            <div class="iw">
                <input type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus placeholder="john@mybusiness.com" />
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
        </div>

        <div class="field">
            <label>New Password</label>
            <div class="iw">
                <input type="password" name="password" required autocomplete="new-password" placeholder="••••••••" />
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>
        </div>

        <div class="field">
            <label>Confirm New Password</label>
            <div class="iw">
                <input type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <button type="submit" class="btn">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Reset Password
        </button>
    </form>

    <a href="{{ route('login') }}" class="back-link">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to Sign In
    </a>
</div>
</body>
</html>
