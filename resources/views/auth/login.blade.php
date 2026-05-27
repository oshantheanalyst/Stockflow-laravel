<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Flow — Sign In</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        const basePath = window.location.pathname.replace(/\/login\/?$/, '');
        const urlParams = new URLSearchParams(window.location.search);
        const googleToken = urlParams.get('google_token');
        const googleUser = urlParams.get('google_user');

        if (googleToken && googleUser) {
            try {
                // 1. Save token and user details to localStorage
                localStorage.setItem('api_token', googleToken);
                localStorage.setItem('api_user', googleUser);
                
                // 2. Set token as cookie
                document.cookie = "api_token=" + googleToken + "; path=/; max-age=86400; samesite=strict";
                
                // 3. Immediately clean up URL query params to avoid exposing tokens in query string permanently
                window.history.replaceState({}, document.title, basePath + '/login');
                
                // 4. Redirect immediately to dashboard
                window.location.replace(window.location.origin + basePath + '/dashboard');
            } catch (e) {
                console.error("Failed to parse Google credentials:", e);
            }
        } else if (localStorage.getItem('api_token') && document.cookie.includes('api_token=')) {
            window.location.replace(window.location.origin + basePath + '/dashboard');
        } else {
            // Clean up potentially stale localStorage if cookie is gone
            localStorage.removeItem('api_token');
            localStorage.removeItem('api_user');
        }
    </script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            background: #F0F4F8;
        }

        /* ── LEFT PANEL ── */
        .left-panel {
            flex: 1;
            background: linear-gradient(160deg, #1E3A5F 0%, #2563EB 60%, #1D4ED8 100%);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 52px 56px;
            position: relative;
            overflow: hidden;
        }
        .left-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        .left-panel::after {
            content: '';
            position: absolute;
            width: 600px; height: 600px;
            border-radius: 50%;
            border: 1px solid rgba(255,255,255,0.07);
            bottom: -200px; right: -200px;
        }
        .circle-decor { position: absolute; width: 400px; height: 400px; border-radius: 50%; border: 1px solid rgba(255,255,255,0.07); bottom: -100px; right: -100px; }
        .circle-decor-2 { position: absolute; width: 250px; height: 250px; border-radius: 50%; border: 1px solid rgba(255,255,255,0.1); top: 80px; right: -60px; }

        .left-logo { display: flex; align-items: center; gap: 12px; position: relative; z-index: 2; }
        .left-logo-icon { width: 46px; height: 46px; background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.25); border-radius: 12px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px); }
        .left-logo-text { font-size: 18px; font-weight: 700; color: white; letter-spacing: -0.3px; }

        .left-content { position: relative; z-index: 2; }
        .left-content h2 { font-size: 38px; font-weight: 800; color: white; line-height: 1.15; letter-spacing: -1px; margin-bottom: 18px; }
        .left-content h2 span { color: #93C5FD; }
        .left-content p { font-size: 15px; color: rgba(255,255,255,0.65); line-height: 1.7; max-width: 340px; }

        .left-stats { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 48px; position: relative; z-index: 2; }
        .stat-item { background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.12); border-radius: 16px; padding: 18px 20px; backdrop-filter: blur(10px); }
        .stat-item .stat-num { font-size: 24px; font-weight: 800; color: white; margin-bottom: 4px; }
        .stat-item .stat-label { font-size: 11px; font-weight: 500; color: rgba(255,255,255,0.5); text-transform: uppercase; letter-spacing: 0.06em; }

        .left-footer { font-size: 12px; color: rgba(255,255,255,0.35); position: relative; z-index: 2; }

        /* ── RIGHT PANEL ── */
        .right-panel { width: 480px; background: #ffffff; display: flex; flex-direction: column; justify-content: center; padding: 60px 52px; box-shadow: -8px 0 40px rgba(0,0,0,0.08); }

        .form-header { margin-bottom: 36px; }
        .form-header h1 { font-size: 26px; font-weight: 800; color: #0F172A; letter-spacing: -0.5px; margin-bottom: 8px; }
        .form-header p { font-size: 14px; color: #64748B; font-weight: 400; }

        .field { margin-bottom: 20px; }
        .field label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 8px; }
        .input-wrap { position: relative; }
        .input-wrap svg { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); width: 17px; height: 17px; color: #9CA3AF; pointer-events: none; transition: color 0.2s; }
        .input-wrap input { width: 100%; height: 48px; padding: 0 16px 0 42px; background: #F8FAFC; border: 1.5px solid #E2E8F0; border-radius: 10px; font-size: 14px; color: #1E293B; font-family: 'Inter', sans-serif; font-weight: 500; outline: none; transition: all 0.2s; }
        .input-wrap input::placeholder { color: #CBD5E1; font-weight: 400; }
        .input-wrap input:focus { border-color: #2563EB; background: #EFF6FF; box-shadow: 0 0 0 4px rgba(37,99,235,0.08); }
        .input-wrap input:focus ~ svg { color: #2563EB; }

        .error-box { background: #FEF2F2; border: 1.5px solid #FCA5A5; border-radius: 10px; padding: 12px 16px; margin-bottom: 20px; display: flex; align-items: flex-start; gap: 10px; }
        .error-box svg { color: #EF4444; width: 16px; height: 16px; flex-shrink: 0; margin-top: 1px; }
        .error-box p { font-size: 13px; color: #DC2626; font-weight: 500; }

        .btn-login { width: 100%; height: 50px; background: #2563EB; border: none; border-radius: 10px; color: white; font-family: 'Inter', sans-serif; font-size: 14px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: all 0.2s; box-shadow: 0 4px 12px rgba(37,99,235,0.25); margin-top: 8px; letter-spacing: 0.01em; }
        .btn-login:hover { background: #1D4ED8; box-shadow: 0 6px 20px rgba(37,99,235,0.35); transform: translateY(-1px); }
        .btn-login:active { transform: translateY(0); box-shadow: 0 2px 8px rgba(37,99,235,0.2); }
        .btn-login:disabled { opacity: 0.65; cursor: not-allowed; transform: none; }
        .btn-login svg { width: 16px; height: 16px; transition: transform 0.2s; }
        .btn-login:hover:not(:disabled) svg { transform: translateX(3px); }

        .divider-line { display: flex; align-items: center; gap: 12px; margin: 28px 0; }
        .divider-line hr { flex: 1; border: none; border-top: 1px solid #E2E8F0; }
        .divider-line span { font-size: 12px; color: #94A3B8; white-space: nowrap; }

        .hint-box { background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; padding: 14px 18px; text-align: center; }
        .hint-box p { font-size: 12px; color: #64748B; margin-bottom: 6px; }
        .hint-box p:last-child { margin-bottom: 0; }
        .hint-box code { background: white; border: 1px solid #E2E8F0; border-radius: 5px; padding: 2px 8px; font-size: 12px; color: #2563EB; font-family: 'Courier New', monospace; font-weight: 600; }

        .btn-google { width: 100%; height: 50px; background: #ffffff; border: 1.5px solid #E2E8F0; border-radius: 10px; color: #1E293B; font-family: 'Inter', sans-serif; font-size: 14px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px; transition: all 0.2s; margin-top: 12px; letter-spacing: 0.01em; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
        .btn-google:hover { background: #F8FAFC; border-color: #CBD5E1; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .btn-google:active { transform: translateY(0); box-shadow: none; }
        .btn-google svg { width: 18px; height: 18px; }

        .form-footer { margin-top: 32px; text-align: center; font-size: 12px; color: #94A3B8; }

        @media (max-width: 768px) {
            .left-panel { display: none; }
            .right-panel { width: 100%; padding: 40px 28px; }
        }
    </style>
</head>
<body>
    <!-- Left Branding Panel -->
    <div class="left-panel">
        <div class="circle-decor"></div>
        <div class="circle-decor-2"></div>

        <div class="left-logo">
            <div class="left-logo-icon">
                <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2L2 7l10 5 10-5-10-5z" />
                    <path d="M2 17l10 5 10-5" />
                    <path d="M2 12l10 5 10-5" />
                    <path d="M12 22V12" stroke="#60A5FA" stroke-width="3" />
                    <path d="M12 12l4-4" stroke="#60A5FA" stroke-width="3" />
                </svg>
            </div>
            <span class="left-logo-text">Stock Flow</span>
        </div>

        <div class="left-content">
            <h2>Smart Business<br><span>Management</span><br>Platform</h2>
            <p>Manage your inventory, sales, expenses, suppliers, and business reports all in one centralized workspace.</p>
            <div class="left-stats">
                <div class="stat-item"><div class="stat-num">360°</div><div class="stat-label">Business View</div></div>
                <div class="stat-item"><div class="stat-num">Live</div><div class="stat-label">Stock Tracking</div></div>
                <div class="stat-item"><div class="stat-num">Auto</div><div class="stat-label">Report Generation</div></div>
                <div class="stat-item"><div class="stat-num">Safe</div><div class="stat-label">Role-Based Access</div></div>
            </div>
        </div>

        <div class="left-footer">&copy; {{ date('Y') }} Stock Flow Business Manager</div>
    </div>

    <!-- Right Login Panel -->
    <div class="right-panel">
        <div class="form-header">
            <h1>Sign In</h1>
            <p>Enter your credentials to access your workspace.</p>
        </div>

        <!-- Session Error Box -->
        @if ($errors->any())
            <div id="error-box" class="error-box" style="display:flex;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p id="error-message">{{ $errors->first() }}</p>
            </div>
        @endif

        <form id="loginForm" method="POST" action="/login">
            @csrf
            <div class="field">
                <label for="username">Username</label>
                <div class="input-wrap">
                    <input type="text" id="username" name="username" value="{{ old('username') }}" autofocus autocomplete="username" placeholder="Your username" required />
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
            </div>

            <div class="field">
                <label for="password">Password</label>
                <div class="input-wrap">
                    <input type="password" id="password" name="password" autocomplete="current-password" placeholder="••••••••" required />
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
            </div>

            <button type="submit" id="loginBtn" class="btn-login">
                <span id="loginBtnText">Sign In to Workspace</span>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </button>

            <button type="button" id="googleLoginBtn" class="btn-google">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" fill="#FBBC05"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                </svg>
                <span>Continue with Google</span>
            </button>
        </form>

        <p style="margin-top: 20px; text-align: center; font-size: 13px; color: #64748B; font-weight: 500;">
            Don't have an account? <a href="#" id="signUpLink" style="color: #2563EB; font-weight: 700; text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='#1D4ED8'" onmouseout="this.style.color='#2563EB'">Sign Up</a>
        </p>



        <p class="form-footer">Protected by role-based access control &amp; Laravel Jetstream</p>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const usernameInput = document.getElementById('username');
            const passwordInput = document.getElementById('password');
            const btn = document.getElementById('loginBtn');
            const btnText = document.getElementById('loginBtnText');
            
            // Remove existing error box
            const oldError = document.getElementById('error-box');
            if (oldError) oldError.remove();
            
            btn.disabled = true;
            btnText.textContent = 'Signing In...';
            
            try {
                // Safely build the API URL relative to the current page path,
                // supporting both artisan serve (root) and XAMPP (subdirectories)
                const basePath = window.location.pathname.replace(/\/login\/?$/, '');
                const apiUrl = window.location.origin + basePath + '/api/auth/login';

                const response = await fetch(apiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        username: usernameInput.value,
                        password: passwordInput.value
                    })
                });
                
                const result = await response.json();
                
                if (response.ok && result.success) {
                    // Store the Sanctum token for apiRequest() calls
                    localStorage.setItem('api_token', result.token);
                    localStorage.setItem('api_user', JSON.stringify(result.user));
                    
                    // Set token as a cookie so backend web middleware can verify it
                    document.cookie = "api_token=" + result.token + "; path=/; max-age=86400; samesite=strict";
                    
                    // Redirect directly to dashboard on successful API login
                    window.location.href = window.location.origin + basePath + '/dashboard';
                } else {
                    showError(result.message || 'These credentials do not match our records.');
                    btn.disabled = false;
                    btnText.textContent = 'Sign In to Workspace';
                }
            } catch (err) {
                console.error("Login Fetch Error:", err);
                showError('Error connecting to server. Check console for details: ' + err.message);
                btn.disabled = false;
                btnText.textContent = 'Sign In to Workspace';
            }
        });

        const googleLoginBtn = document.getElementById('googleLoginBtn');
        if (googleLoginBtn) {
            googleLoginBtn.addEventListener('click', function() {
                const basePath = window.location.pathname.replace(/\/login\/?$/, '');
                window.location.href = window.location.origin + basePath + '/api/auth/google/redirect';
            });
        }

        const signUpLink = document.getElementById('signUpLink');
        if (signUpLink) {
            const basePath = window.location.pathname.replace(/\/login\/?$/, '');
            signUpLink.href = window.location.origin + basePath + '/register';
        }

        function showError(msg) {
            const oldError = document.getElementById('error-box');
            if (oldError) oldError.remove();

            const container = document.querySelector('.form-header');
            const box = document.createElement('div');
            box.id = 'error-box';
            box.className = 'error-box';
            box.style.display = 'flex';
            box.innerHTML = `
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p id="error-message">${msg}</p>
            `;
            container.parentNode.insertBefore(box, document.getElementById('loginForm'));
        }
    </script>
</body>
</html>
