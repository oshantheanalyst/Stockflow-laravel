<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Flow — Forgot Password</title>
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
        .left-body p{font-size:14px;color:rgba(255,255,255,0.65);line-height:1.7;max-width:320px;margin-bottom:32px}
        .step{display:flex;align-items:flex-start;gap:14px;padding:16px 0;border-bottom:1px solid rgba(255,255,255,0.08)}
        .step:last-child{border-bottom:none}
        .step-num{width:30px;height:30px;border-radius:50%;background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:800;color:white;flex-shrink:0}
        .step strong{display:block;font-size:13px;font-weight:700;color:white;margin-bottom:2px}
        .step span{font-size:11px;color:rgba(255,255,255,0.55)}
        .left-foot{font-size:12px;color:rgba(255,255,255,0.35);position:relative;z-index:2}
        .right-panel{width:480px;background:#fff;display:flex;flex-direction:column;justify-content:center;padding:60px 52px;box-shadow:-8px 0 40px rgba(0,0,0,0.08)}
        .icon-box{width:60px;height:60px;background:#EFF6FF;border-radius:16px;display:flex;align-items:center;justify-content:center;margin-bottom:22px}
        .icon-box svg{width:28px;height:28px;color:#2563EB}
        .fh h1{font-size:24px;font-weight:800;color:#0F172A;margin-bottom:6px}
        .fh p{font-size:14px;color:#64748B;line-height:1.6;margin-bottom:28px}
        .field label{display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:7px}
        .iw{position:relative}
        .iw svg{position:absolute;left:13px;top:50%;transform:translateY(-50%);width:16px;height:16px;color:#9CA3AF;pointer-events:none;transition:color .2s}
        .iw input{width:100%;height:48px;padding:0 16px 0 40px;background:#F8FAFC;border:1.5px solid #E2E8F0;border-radius:10px;font-size:14px;color:#1E293B;font-family:'Inter',sans-serif;font-weight:500;outline:none;transition:all .2s}
        .iw input::placeholder{color:#CBD5E1;font-weight:400}
        .iw input:focus{border-color:#2563EB;background:#EFF6FF;box-shadow:0 0 0 4px rgba(37,99,235,.08)}
        .iw input:focus~svg{color:#2563EB}
        .success-box{background:#F0FDF4;border:1.5px solid #86EFAC;border-radius:10px;padding:14px;margin-bottom:20px;display:flex;gap:10px}
        .success-box svg{width:18px;height:18px;color:#16A34A;flex-shrink:0;margin-top:1px}
        .success-box p{font-size:13px;color:#15803D;font-weight:500;line-height:1.5}
        .err-box{background:#FEF2F2;border:1.5px solid #FCA5A5;border-radius:10px;padding:11px 14px;margin-bottom:16px}
        .err-box p{font-size:12px;color:#DC2626;font-weight:500}
        .btn{width:100%;height:50px;background:#2563EB;border:none;border-radius:10px;color:white;font-family:'Inter',sans-serif;font-size:14px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:all .2s;box-shadow:0 4px 12px rgba(37,99,235,.25);margin-top:20px}
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
        <h2>Reset Your<br><span>Password</span><br>in 3 Steps</h2>
        <p>We'll send a secure reset link directly to your inbox. Quick and safe.</p>
        <div class="step"><div class="step-num">1</div><div><strong>Enter your username or email</strong><span>Use either your username or registered email address.</span></div></div>
        <div class="step"><div class="step-num">2</div><div><strong>Check your inbox</strong><span>A secure reset link will be sent within minutes.</span></div></div>
        <div class="step"><div class="step-num">3</div><div><strong>Set a new password</strong><span>Regain full access to your business workspace.</span></div></div>
    </div>
    <div class="left-foot">&copy; {{ date('Y') }} Stock Flow Business Manager</div>
</div>

<div class="right-panel">
    <div class="icon-box">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
    </div>
    <div class="fh">
        <h1>Forgot your password?</h1>
        <p>Enter your username or registered email, and we'll send you a secure link to reset it.</p>
    </div>

    @if(session('status'))
    <div class="success-box">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <p>{{ session('status') }}</p>
    </div>
    @endif
    @if(session('error'))
    <div class="err-box">
        <p>{{ session('error') }}</p>
    </div>
    @endif
    @if($errors->any())
    <div class="err-box">@foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="field">
            <label>Username or Email Address</label>
            <div class="iw">
                <input type="text" name="email" value="{{ old('email') }}" required autofocus placeholder="john_doe or john@mybusiness.com" />
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
        </div>
        <button type="submit" class="btn">
            Send Reset Link
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:16px;height:16px;flex-shrink:0"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
        </button>
    </form>

    <a href="{{ route('login') }}" class="back-link">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to Sign In
    </a>
</div>
</body>
</html>
