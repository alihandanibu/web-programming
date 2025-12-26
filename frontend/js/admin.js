import { UserService } from './services/userService.js';
import { ProjectService } from './services/projectService.js';
import { SkillService } from './services/skillService.js';
import { ContactService } from './services/contactService.js';
import { getStoredUser } from './services/api.js';
import { withBlockUI, confirmWithModal, notifySuccess, notifyError, notifyInfo } from './validations.js';

function withButtonLock(buttonEl, fn) {
    if (!buttonEl) return fn();
    const prevDisabled = buttonEl.disabled;
    buttonEl.disabled = true;
    return Promise.resolve(fn()).finally(() => {
        buttonEl.disabled = prevDisabled;
    });
}

function isOnAdmin() {
    return window.location.hash === '#admin';
}

function ensureAdminDomReady(thenRun, attemptsLeft = 30) {
    if (!isOnAdmin()) return;

    if (document.getElementById('adminUserEmail')) {
        thenRun();
        return;
    }

    if (attemptsLeft <= 0) return;
    setTimeout(() => ensureAdminDomReady(thenRun, attemptsLeft - 1), 80);
}

async function loadAdminUsers() {
    const container = document.getElementById('adminUsersContainer');
    if (!container) return;

    container.innerHTML =
        '<div class="text-center py-4">' +
        '<div class="spinner-border text-primary" role="status"></div>' +
        '<p class="text-muted mt-2">Loading users...</p>' +
        '</div>';

    try {
        const users = await UserService.list();

        if (!users.length) {
            container.innerHTML = '<p class="text-muted text-center">No registered users found.</p>';
            return;
        }

        let html =
            '<div class="table-responsive"><table class="table table-hover">' +
            '<thead class="table-light"><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Actions</th></tr></thead><tbody>';

        const currentUser = getStoredUser() || {};

        users.forEach(u => {
            const roleClass = String(u.role || '').toLowerCase() === 'admin' ? 'bg-danger' : 'bg-secondary';
            const isCurrentUser = String(currentUser.id) === String(u.id);
            const currentRole = (u.role || 'user').toLowerCase();

            // Role dropdown for non-current users
            const roleCell = isCurrentUser
                ? `<span class="badge ${roleClass}">${currentRole.toUpperCase()}</span>`
                : `<select class="form-select form-select-sm role-select" data-id="${u.id}" data-original="${currentRole}" style="width: auto; display: inline-block;">
                    <option value="user" ${currentRole === 'user' ? 'selected' : ''}>USER</option>
                    <option value="admin" ${currentRole === 'admin' ? 'selected' : ''}>ADMIN</option>
                   </select>`;

            html +=
                '<tr>' +
                `<td>${u.id}</td>` +
                `<td>${u.name || '<span class="text-muted">-</span>'}</td>` +
                `<td>${u.email || ''}</td>` +
                `<td>${roleCell}</td>` +
                '<td>' +
                (isCurrentUser
                    ? '<span class="text-muted small">Current user</span>'
                    : `<button class="btn btn-sm btn-outline-danger delete-user-btn" data-id="${u.id}" data-email="${u.email || u.id}"><i class="fas fa-trash"></i> Delete</button>`)
                + '</td></tr>';
        });

        html += '</tbody></table></div>';
        container.innerHTML = html;
    } catch (err) {
        console.error('Load users error:', err);
        container.innerHTML = '<div class="alert alert-danger">Failed to load users. Please try again.</div>';
    }
}

// Store analytics data for charts
let analyticsData = {
    users: [],
    userCount: 0,
    adminCount: 0,
    projectCount: 0,
    skillCount: 0,
    userStats: [] // per-user stats
};

async function loadAdminStats() {
    const totalEl = document.getElementById('statTotalUsers');
    const adminEl = document.getElementById('statAdminCount');
    const projEl = document.getElementById('statProjectCount');
    const skillEl = document.getElementById('statSkillCount');

    try {
        const users = await UserService.list();
        analyticsData.users = users;
        analyticsData.userCount = users.length;

        if (totalEl) totalEl.textContent = users.length;
        const adminCount = users.filter(u => String(u.role || '').toLowerCase() === 'admin').length;
        analyticsData.adminCount = adminCount;
        if (adminEl) adminEl.textContent = adminCount;

        let totalProjects = 0;
        let totalSkills = 0;
        const userStats = [];

        const results = await Promise.all(users.map(async u => {
            const [projects, skills] = await Promise.all([
                ProjectService.listByUser(u.id).catch(() => []),
                SkillService.listByUser(u.id).catch(() => [])
            ]);
            return {
                userId: u.id,
                name: u.name || u.email,
                projects: projects.length,
                skills: skills.length
            };
        }));

        results.forEach(r => {
            totalProjects += r.projects;
            totalSkills += r.skills;
            userStats.push(r);
        });

        analyticsData.projectCount = totalProjects;
        analyticsData.skillCount = totalSkills;
        analyticsData.userStats = userStats;

        if (projEl) projEl.textContent = totalProjects;
        if (skillEl) skillEl.textContent = totalSkills;

        // Render charts after data is loaded
        renderAnalyticsCharts();
    } catch {
        if (totalEl) totalEl.textContent = '0';
        if (adminEl) adminEl.textContent = '0';
        if (projEl) projEl.textContent = '0';
        if (skillEl) skillEl.textContent = '0';
    }
}

/* =========================
   CONTACTS MANAGEMENT
========================= */
async function loadAdminContacts(statusFilter = null) {
    const container = document.getElementById('adminContactsContainer');
    if (!container) return;

    container.innerHTML =
        '<div class="text-center py-4">' +
        '<div class="spinner-border text-success" role="status"></div>' +
        '<p class="text-muted mt-2">Loading contacts...</p>' +
        '</div>';

    try {
        const user = getStoredUser();
        const result = await ContactService.list(user.id, statusFilter);
        const contacts = result.contacts || [];

        if (!contacts.length) {
            container.innerHTML = '<p class="text-muted text-center py-3">No contact messages found.</p>';
            return;
        }

        let html =
            '<div class="table-responsive"><table class="table table-hover table-sm">' +
            '<thead class="table-light"><tr><th>ID</th><th>Name</th><th>Email</th><th>Message</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead><tbody>';

        contacts.forEach(c => {
            const statusClass = {
                'unread': 'bg-warning text-dark',
                'read': 'bg-info',
                'replied': 'bg-success'
            }[c.status] || 'bg-secondary';

            // Truncate message for display
            const msgPreview = (c.message || '').substring(0, 50) + ((c.message || '').length > 50 ? '...' : '');
            const createdAt = c.created_at ? new Date(c.created_at).toLocaleDateString() : '-';

            html +=
                '<tr>' +
                `<td>${c.id}</td>` +
                `<td>${escapeHtml(c.name || '-')}</td>` +
                `<td><a href="mailto:${escapeHtml(c.email)}">${escapeHtml(c.email || '-')}</a></td>` +
                `<td title="${escapeHtml(c.message || '')}">${escapeHtml(msgPreview)}</td>` +
                `<td><span class="badge ${statusClass}">${c.status || 'unread'}</span></td>` +
                `<td>${createdAt}</td>` +
                '<td class="text-nowrap">' +
                `<button class="btn btn-xs btn-outline-primary mark-read-btn me-1" data-id="${c.id}" data-status="read" title="Mark as read"><i class="fas fa-check"></i></button>` +
                `<button class="btn btn-xs btn-outline-success mark-replied-btn me-1" data-id="${c.id}" data-status="replied" title="Mark as replied"><i class="fas fa-reply"></i></button>` +
                `<button class="btn btn-xs btn-outline-danger delete-contact-btn" data-id="${c.id}" data-email="${escapeHtml(c.email || c.id)}"><i class="fas fa-trash"></i></button>` +
                '</td></tr>';
        });

        html += '</tbody></table></div>';
        container.innerHTML = html;
    } catch (err) {
        console.error('Load contacts error:', err);
        container.innerHTML = '<div class="alert alert-danger">Failed to load contacts.</div>';
    }
}

function escapeHtml(str) {
    return String(str ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

/* =========================
   ANALYTICS CHARTS
========================= */
let chartInstances = {};

function renderAnalyticsCharts() {
    // Destroy previous chart instances
    Object.values(chartInstances).forEach(chart => {
        if (chart && typeof chart.destroy === 'function') {
            chart.destroy();
        }
    });
    chartInstances = {};

    // Check if Chart.js is available
    if (typeof Chart === 'undefined') {
        console.warn('Chart.js not loaded, skipping charts');
        return;
    }

    // 1. User Distribution Pie Chart (Users vs Admins)
    const userDistCtx = document.getElementById('userDistributionChart');
    if (userDistCtx) {
        chartInstances.userDist = new Chart(userDistCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Regular Users', 'Admins'],
                datasets: [{
                    data: [
                        analyticsData.userCount - analyticsData.adminCount,
                        analyticsData.adminCount
                    ],
                    backgroundColor: ['#6c757d', '#dc3545'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' },
                    title: { display: true, text: 'User Roles Distribution' }
                }
            }
        });
    }

    // 2. Overview Bar Chart (Total Projects, Skills, Users)
    const overviewCtx = document.getElementById('overviewChart');
    if (overviewCtx) {
        chartInstances.overview = new Chart(overviewCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['Users', 'Projects', 'Skills'],
                datasets: [{
                    label: 'Total Count',
                    data: [
                        analyticsData.userCount,
                        analyticsData.projectCount,
                        analyticsData.skillCount
                    ],
                    backgroundColor: ['#0d6efd', '#198754', '#0dcaf0'],
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    title: { display: true, text: 'Platform Overview' }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });
    }

    // 3. Top Users by Content (Bar chart)
    const topUsersCtx = document.getElementById('topUsersChart');
    if (topUsersCtx && analyticsData.userStats.length > 0) {
        // Sort by total content (projects + skills) and take top 5
        const topUsers = [...analyticsData.userStats]
            .map(u => ({ ...u, total: u.projects + u.skills }))
            .sort((a, b) => b.total - a.total)
            .slice(0, 5);

        chartInstances.topUsers = new Chart(topUsersCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: topUsers.map(u => u.name.length > 12 ? u.name.substring(0, 12) + '...' : u.name),
                datasets: [
                    {
                        label: 'Projects',
                        data: topUsers.map(u => u.projects),
                        backgroundColor: '#198754'
                    },
                    {
                        label: 'Skills',
                        data: topUsers.map(u => u.skills),
                        backgroundColor: '#0dcaf0'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' },
                    title: { display: true, text: 'Top 5 Users by Content' }
                },
                scales: {
                    x: { stacked: true },
                    y: { stacked: true, beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });
    }
}

function initAdmin() {
    if (!isOnAdmin()) return;

    const token = localStorage.getItem('token');
    const user = getStoredUser();

    if (!token || !user) {
        window.location.hash = '#login';
        return;
    }

    if (user.role !== 'admin') {
        window.location.hash = '#home';
        return;
    }

    ensureAdminDomReady(() => {
        const emailEl = document.getElementById('adminUserEmail');
        if (emailEl) emailEl.textContent = user.email || '';

        withBlockUI(async () => {
            await Promise.all([loadAdminUsers(), loadAdminStats(), loadAdminContacts()]);
        }, 'Loading admin panel...');

        // Setup contacts filter change listener
        const filterEl = document.getElementById('contactStatusFilter');
        if (filterEl) {
            filterEl.addEventListener('change', () => {
                const status = filterEl.value || null;
                loadAdminContacts(status);
            });
        }
    });
}

document.addEventListener('DOMContentLoaded', initAdmin);
window.addEventListener('hashchange', initAdmin);

document.addEventListener('click', async (e) => {
    const del = e.target.closest('.delete-user-btn');
    if (del) {
        const id = del.dataset.id;
        const email = del.dataset.email || id;

        const ok = await confirmWithModal({
            title: 'Delete user',
            message: `Are you sure you want to delete user "${email}"? This action cannot be undone.`,
            okText: 'Delete',
            okBtnClass: 'btn-danger'
        });
        if (!ok) return;

        try {
            await withButtonLock(del, async () => {
                await withBlockUI(async () => {
                    await UserService.remove(id);
                    await Promise.all([loadAdminUsers(), loadAdminStats()]);
                }, 'Deleting user...');
            });
            notifySuccess('User deleted successfully.');
        } catch (err) {
            notifyError(err.message || 'Failed to delete user');
        }
    }

    const refreshBtn = e.target.closest('#refreshUsersBtn');
    if (refreshBtn) {
        await withButtonLock(refreshBtn, async () => {
            notifyInfo('Refreshing...');
            await withBlockUI(async () => {
                await Promise.all([loadAdminUsers(), loadAdminStats()]);
            }, 'Refreshing...');
        });
    }

    // Refresh contacts button
    const refreshContactsBtn = e.target.closest('#refreshContactsBtn');
    if (refreshContactsBtn) {
        await withButtonLock(refreshContactsBtn, async () => {
            const filterEl = document.getElementById('contactStatusFilter');
            const status = filterEl ? filterEl.value || null : null;
            await loadAdminContacts(status);
        });
    }

    // Delete contact button
    const delContact = e.target.closest('.delete-contact-btn');
    if (delContact) {
        const contactId = delContact.dataset.id;
        const email = delContact.dataset.email || contactId;

        const ok = await confirmWithModal({
            title: 'Delete contact',
            message: `Are you sure you want to delete the message from "${email}"?`,
            okText: 'Delete',
            okBtnClass: 'btn-danger'
        });
        if (!ok) return;

        try {
            const user = getStoredUser();
            await withButtonLock(delContact, async () => {
                await withBlockUI(async () => {
                    await ContactService.remove(user.id, contactId);
                    const filterEl = document.getElementById('contactStatusFilter');
                    const status = filterEl ? filterEl.value || null : null;
                    await loadAdminContacts(status);
                }, 'Deleting contact...');
            });
            notifySuccess('Contact deleted successfully.');
        } catch (err) {
            notifyError(err.message || 'Failed to delete contact');
        }
    }

    // Mark contact as read/replied
    const markBtn = e.target.closest('.mark-read-btn, .mark-replied-btn');
    if (markBtn) {
        const contactId = markBtn.dataset.id;
        const newStatus = markBtn.dataset.status;

        try {
            const user = getStoredUser();
            await withButtonLock(markBtn, async () => {
                await ContactService.updateStatus(user.id, contactId, newStatus);
                const filterEl = document.getElementById('contactStatusFilter');
                const status = filterEl ? filterEl.value || null : null;
                await loadAdminContacts(status);
            });
            notifySuccess(`Contact marked as ${newStatus}.`);
        } catch (err) {
            notifyError(err.message || 'Failed to update contact status');
        }
    }
});

// Role change handler (separate event for select elements)
document.addEventListener('change', async (e) => {
    const roleSelect = e.target.closest('.role-select');
    if (roleSelect) {
        const userId = roleSelect.dataset.id;
        const originalRole = roleSelect.dataset.original;
        const newRole = roleSelect.value;

        if (newRole === originalRole) return;

        const ok = await confirmWithModal({
            title: 'Change User Role',
            message: `Are you sure you want to change this user's role from ${originalRole.toUpperCase()} to ${newRole.toUpperCase()}?`,
            okText: 'Change Role',
            okBtnClass: newRole === 'admin' ? 'btn-danger' : 'btn-primary'
        });

        if (!ok) {
            // Revert selection
            roleSelect.value = originalRole;
            return;
        }

        try {
            roleSelect.disabled = true;
            await withBlockUI(async () => {
                await UserService.updateRole(userId, newRole);
                await Promise.all([loadAdminUsers(), loadAdminStats()]);
            }, 'Updating role...');
            notifySuccess(`User role changed to ${newRole.toUpperCase()}.`);
        } catch (err) {
            roleSelect.value = originalRole;
            notifyError(err.message || 'Failed to update role');
        } finally {
            roleSelect.disabled = false;
        }
    }
});
