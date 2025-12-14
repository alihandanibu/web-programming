// Admin page initialization
document.addEventListener('DOMContentLoaded', function () {
    // Check if we're on admin page
    if (window.location.hash !== '#admin') {
        return;
    }

    // Check authentication and role
    const token = localStorage.getItem('token');
    const user = localStorage.getItem('user');

    if (!token || !user) {
        window.location.hash = '#login';
        return;
    }

    const userData = JSON.parse(user);

    // Check if user is admin
    if (userData.role !== 'admin') {
        window.location.hash = '#home';
        return;
    }

    // Display admin user email
    const emailEl = document.getElementById('adminUserEmail');
    if (emailEl) {
        emailEl.textContent = userData.email;
    }
});

// Listen for hash changes to reload admin page
window.addEventListener('hashchange', function () {
    if (window.location.hash === '#admin') {
        const token = localStorage.getItem('token');
        const user = localStorage.getItem('user');

        if (!token || !user) {
            window.location.hash = '#login';
            return;
        }

        const userData = JSON.parse(user);

        if (userData.role !== 'admin') {
            window.location.hash = '#home';
            return;
        }

        // Update user email if element exists
        const emailEl = document.getElementById('adminUserEmail');
        if (emailEl && userData.email) {
            emailEl.textContent = userData.email;
        }
    }
});
