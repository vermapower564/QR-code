/**
 * Global Smart Navigation Helper (Back & Next) for QR Identity SaaS
 */
function navigateApp(direction) {
    const path = (window.location.pathname || '/').replace(/\/$/, '') || '/';

    if (direction === 'back') {
        // If history exists within current domain, navigate back
        if (window.history.length > 1 && document.referrer && document.referrer.indexOf(window.location.host) !== -1 && document.referrer !== window.location.href) {
            window.history.back();
            return;
        }

        // Logical fallback back routes
        const backMap = {
            '/features': '/',
            '/pricing': '/features',
            '/how-it-works': '/pricing',
            '/about': '/',
            '/contact': '/',
            '/terms': '/',
            '/privacy': '/',
            '/refund-policy': '/',
            '/cookie-policy': '/',
            '/login': '/',
            '/register': '/',
            '/forgot-password': '/login',
            '/dashboard': '/',
            '/dashboard/profiles': '/dashboard',
            '/dashboard/profiles/create': '/dashboard/profiles',
            '/dashboard/billing': '/dashboard',
            '/dashboard/settings': '/dashboard',
            '/dashboard/notifications': '/dashboard',
            '/dashboard/domains': '/dashboard',
            '/admin': '/dashboard',
            '/admin/users': '/admin',
            '/admin/profiles': '/admin',
            '/admin/plans': '/admin',
            '/admin/templates': '/admin',
            '/admin/domains': '/admin',
            '/admin/reports': '/admin',
            '/admin/audit-logs': '/admin'
        };

        if (backMap[path]) {
            window.location.href = backMap[path];
        } else if (path.startsWith('/dashboard/profiles/')) {
            window.location.href = '/dashboard/profiles';
        } else if (path.startsWith('/admin/')) {
            window.location.href = '/admin';
        } else {
            window.history.back();
        }
    } else if (direction === 'next') {
        // Logical forward routes
        const nextMap = {
            '/': '/features',
            '/features': '/pricing',
            '/pricing': '/how-it-works',
            '/how-it-works': '/register',
            '/login': '/dashboard',
            '/register': '/dashboard',
            '/dashboard': '/dashboard/profiles',
            '/dashboard/profiles': '/dashboard/profiles/create',
            '/dashboard/profiles/create': '/dashboard/billing',
            '/dashboard/billing': '/dashboard/settings',
            '/dashboard/settings': '/dashboard',
            '/admin': '/admin/users',
            '/admin/users': '/admin/profiles',
            '/admin/profiles': '/admin/plans',
            '/admin/plans': '/admin/templates',
            '/admin/templates': '/admin/domains',
            '/admin/domains': '/admin/reports',
            '/admin/reports': '/admin/audit-logs',
            '/admin/audit-logs': '/admin'
        };

        if (nextMap[path]) {
            window.location.href = nextMap[path];
        } else {
            window.history.forward();
        }
    }
}

/**
 * Background Session Keep-Alive & CSRF Token Synchronizer
 * Keeps sessions active while users are typing and auto-refreshes tokens
 */
(function () {
    let lastPing = Date.now();

    function syncSession() {
        fetch('/session/keep-alive', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(function (res) { return res.json(); })
        .then(function (data) {
            if (data && data.csrf) {
                const meta = document.querySelector('meta[name="csrf-token"]');
                if (meta) meta.setAttribute('content', data.csrf);

                const forms = document.querySelectorAll('form');
                forms.forEach(function (form) {
                    const tokenInput = form.querySelector('input[name="_token"]');
                    if (tokenInput) {
                        tokenInput.value = data.csrf;
                    }
                });
            }
            lastPing = Date.now();
        })
        .catch(function () {});
    }

    // Ping every 5 minutes to prevent session expiration
    setInterval(syncSession, 5 * 60 * 1000);

    // If tab was left open in background and user comes back after 10 minutes, sync immediately
    document.addEventListener('visibilitychange', function () {
        if (!document.hidden && (Date.now() - lastPing > 10 * 60 * 1000)) {
            syncSession();
        }
    });
})();
