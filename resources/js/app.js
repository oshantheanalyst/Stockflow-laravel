import './bootstrap';

/**
 * ============================================================
 * STOCKFLOW — Global API Client
 * ============================================================
 *
 * Every data operation in this application goes through apiRequest().
 * The Bearer token is fetched from localStorage (set at login).
 *
 * IF routes/api.php IS DISABLED:
 *   - Every apiRequest() call returns a 404
 *   - Pages show empty data
 *   - All CRUD operations fail
 *   - The application is completely non-functional
 *
 * This is intentional by design (true API-first architecture).
 */

/**
 * Make an authenticated API request to /api/* endpoint.
 * @param {string} method  - HTTP method (GET, POST, PUT, PATCH, DELETE)
 * @param {string} endpoint - API path without /api prefix (e.g. '/products')
 * @param {object|null} data - Request body (for POST/PUT)
 * @returns {Promise<object>} - Parsed JSON response
 */
window.apiRequest = async function(method, endpoint, data = null) {
    const token = localStorage.getItem('api_token');

    // Dynamically compute the base path to support XAMPP subdirectories vs artisan serve
    const _pathMatch = window.location.pathname.match(/^(.*?)\/(?:dashboard|login|products|categories|orders|customers|suppliers|expenses|sales|reminders|reports|users|profile)(?:\/|$)/);
    const _basePath = _pathMatch ? _pathMatch[1] : '';

    if (!token) {
        window.location.href = _basePath + '/login';
        return;
    }

    const opts = {
        method: method.toUpperCase(),
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`,
        },
    };

    if (data && method !== 'GET') {
        opts.body = JSON.stringify(data);
    }

    const url = _basePath + '/api' + endpoint;

    try {
        const res = await fetch(url, opts);

        if (res.status === 401) {
            localStorage.removeItem('api_token');
            localStorage.removeItem('api_user');
            document.cookie = "api_token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
            window.location.href = _basePath + '/login';
            return;
        }

        // Forbidden
        if (res.status === 403) {
            showToast('You do not have permission to perform this action.', 'error');
            return null;
        }

        const json = await res.json();

        // Validation errors (422)
        if (res.status === 422 && json.errors) {
            const messages = Object.values(json.errors).flat().join('\n');
            showToast(messages, 'error');
            return null;
        }

        if (!res.ok) {
            showToast(json.message || 'An error occurred.', 'error');
            return null;
        }

        return json;

    } catch (err) {
        // Network error or api.php disabled
        showToast('API unavailable. Check if routes/api.php is loaded.', 'error');
        return null;
    }
};

/**
 * Get the current logged-in user from localStorage.
 * Set at login time from the /api/auth/login response.
 */
window.getCurrentUser = function() {
    try {
        return JSON.parse(localStorage.getItem('api_user') || 'null');
    } catch {
        return null;
    }
};

/**
 * Show a toast notification (success or error).
 * Works on any page that has the layout's notification area.
 */
window.showToast = function(message, type = 'success') {
    // Remove existing toasts
    document.querySelectorAll('.sf-toast').forEach(t => t.remove());

    const toast = document.createElement('div');
    toast.className = 'sf-toast';
    toast.style.cssText = `
        position: fixed; top: 24px; right: 24px; z-index: 9999;
        padding: 12px 20px; border-radius: 12px; font-size: 13px; font-weight: 600;
        display: flex; align-items: center; gap: 8px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.12);
        animation: slideInRight 0.25s ease;
        max-width: 400px;
        ${type === 'success'
            ? 'background: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0;'
            : 'background: #FEF2F2; color: #991B1B; border: 1px solid #FECACA;'}
    `;

    const icon = type === 'success'
        ? '<svg style="width:16px;height:16px;flex-shrink:0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
        : '<svg style="width:16px;height:16px;flex-shrink:0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';

    toast.innerHTML = icon + `<span>${message}</span>`;

    if (!document.getElementById('sf-toast-style')) {
        const style = document.createElement('style');
        style.id = 'sf-toast-style';
        style.textContent = `@keyframes slideInRight { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }`;
        document.head.appendChild(style);
    }

    document.body.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3500);
};

/**
 * Format a number as LKR currency.
 */
window.formatCurrency = function(value) {
    return 'Rs ' + parseFloat(value || 0).toLocaleString('en-LK', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
};

/**
 * Modal helpers
 */
window.openModal = function(id) {
    const el = document.getElementById(id);
    if (el) el.classList.add('active');
};

window.closeModal = function(id) {
    const el = document.getElementById(id);
    if (el) el.classList.remove('active');
};

/**
 * Logout — calls DELETE /api/auth/logout to revoke Sanctum token,
 * then clears localStorage and redirects to /login.
 */
window.logoutUser = async function() {
    try {
        await apiRequest('POST', '/auth/logout');
    } catch (_) { /* ignore */ }
    localStorage.removeItem('api_token');
    localStorage.removeItem('api_user');
    document.cookie = "api_token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    const logoutMatch = window.location.pathname.match(/^(.*?)\/(?:dashboard|login|products|categories|orders|customers|suppliers|expenses|sales|reminders|reports|users|profile)(?:\/|$)/);
    const logoutBase = logoutMatch ? logoutMatch[1] : '';
    window.location.href = logoutBase + '/login';
};


