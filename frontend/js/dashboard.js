// Dashboard initialization
document.addEventListener('DOMContentLoaded', function () {
    // Check if we're on the dashboard view
    if (window.location.hash !== '#dashboard') {
        return;
    }

    // Check authentication
    const token = localStorage.getItem('token');
    if (!token) {
        window.location.hash = '#login';
        return;
    }

    // Load dashboard data
    loadDashboard();

    // Setup logout button
    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function () {
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            window.location.hash = '#home';
        });
    }
});

// Listen for hash changes to reload dashboard
window.addEventListener('hashchange', function () {
    if (window.location.hash === '#dashboard') {
        const token = localStorage.getItem('token');
        if (!token) {
            window.location.hash = '#login';
            return;
        }
        loadDashboard();
    }
});

function loadDashboard() {
    // Get current session time
    const now = new Date();
    const sessionTimeEl = document.getElementById('sessionTime');
    if (sessionTimeEl) {
        sessionTimeEl.textContent = now.toLocaleString();
    }

    // Load user data from backend
    apiGetCurrentUser()
        .then(response => {
            if (response.success && response.user) {
                const user = response.user;

                // Update welcome message
                const welcomeMsg = document.getElementById('welcomeMessage');
                if (welcomeMsg) {
                    welcomeMsg.textContent = `Welcome back, ${user.email.split('@')[0]}!`;
                }

                // Update user info
                const userNameEl = document.getElementById('userName');
                if (userNameEl) {
                    userNameEl.textContent = user.email.split('@')[0];
                }

                const userEmailEl = document.getElementById('userEmail');
                if (userEmailEl) {
                    userEmailEl.textContent = user.email;
                }

                const userRoleEl = document.getElementById('userRole');
                if (userRoleEl) {
                    const roleClass = user.role === 'admin' ? 'bg-danger' : 'bg-info';
                    userRoleEl.innerHTML = `<span class="badge ${roleClass}">${user.role.toUpperCase()}</span>`;
                }

                // Show role-based section
                const adminSection = document.getElementById('adminSection');
                const userSection = document.getElementById('userSection');

                if (user.role === 'admin') {
                    if (adminSection) adminSection.style.display = 'block';
                    if (userSection) userSection.style.display = 'none';
                } else {
                    if (adminSection) adminSection.style.display = 'none';
                    if (userSection) userSection.style.display = 'block';
                }

                // Hide error
                const errorDiv = document.getElementById('dashboardError');
                if (errorDiv) {
                    errorDiv.style.display = 'none';
                }
            } else {
                showDashboardError('Failed to load user data');
            }
        })
        .catch(error => {
            console.error('Dashboard error:', error);
            showDashboardError(error.message || 'Failed to connect to backend');
        });
}

function showDashboardError(message) {
    const errorDiv = document.getElementById('dashboardError');
    const errorMsg = document.getElementById('errorMessage');

    if (errorDiv && errorMsg) {
        errorMsg.textContent = message;
        errorDiv.style.display = 'block';
    }
}
