function isOnAdmin() {
    return window.location.hash === '#admin';
}

function ensureAdminDomReady(thenRun, attemptsLeft = 25) {
    if (!isOnAdmin()) {
        return;
    }

    if (document.getElementById('adminUserEmail')) {
        thenRun();
        return;
    }

    if (attemptsLeft <= 0) {
        return;
    }

    setTimeout(() => ensureAdminDomReady(thenRun, attemptsLeft - 1), 80);
}

function initAdmin() {
    if (!isOnAdmin()) {
        return;
    }

    const token = localStorage.getItem('token');
    const user = localStorage.getItem('user');

    if (!token || !user) {
        window.location.hash = '#login';
        return;
    }

    let userData;
    try {
        userData = JSON.parse(user);
    } catch (e) {
        window.location.hash = '#login';
        return;
    }

    if (userData.role !== 'admin') {
        window.location.hash = '#home';
        return;
    }

    ensureAdminDomReady(function () {
        const emailEl = document.getElementById('adminUserEmail');
        if (emailEl && userData.email) {
            emailEl.textContent = userData.email;
        }
    });
}

document.addEventListener('DOMContentLoaded', initAdmin);
window.addEventListener('hashchange', initAdmin);
