function isOnDashboard() {
    return window.location.hash === '#dashboard';
}

function ensureDashboardDomReady(thenRun, attemptsLeft = 25) {
    if (!isOnDashboard()) {
        return;
    }

    // Wait for SPA loader to inject the view DOM
    if (document.getElementById('welcomeMessage')) {
        thenRun();
        return;
    }

    if (attemptsLeft <= 0) {
        showDashboardError('Dashboard view did not load. Please refresh.');
        return;
    }

    setTimeout(() => ensureDashboardDomReady(thenRun, attemptsLeft - 1), 80);
}

function initDashboard() {
    if (!isOnDashboard()) {
        return;
    }

    const token = localStorage.getItem('token');
    if (!token) {
        window.location.hash = '#login';
        return;
    }

    ensureDashboardDomReady(loadDashboard);
}

document.addEventListener('DOMContentLoaded', initDashboard);
window.addEventListener('hashchange', initDashboard);

document.addEventListener('click', function (e) {
    const target = e.target;
    const btn = target && (target.id === 'logoutBtn' ? target : target.closest ? target.closest('#logoutBtn') : null);
    if (!btn) {
        return;
    }
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    window.location.hash = '#home';
    location.reload();
});

// Store current userId for CRUD operations
let dashboardUserId = null;

function loadDashboard() {
    const now = new Date();
    const sessionTimeEl = document.getElementById('sessionTime');
    if (sessionTimeEl) {
        sessionTimeEl.textContent = now.toLocaleString();
    }

    apiGetCurrentUser()
        .then(response => {
            if (response.success && response.user) {
                const user = response.user;
                dashboardUserId = user.id;

                const displayName = (user.name && String(user.name).trim()) ? String(user.name).trim() : user.email.split('@')[0];

                const welcomeMsg = document.getElementById('welcomeMessage');
                if (welcomeMsg) {
                    welcomeMsg.textContent = `Welcome back, ${displayName}!`;
                }

                const userNameEl = document.getElementById('userName');
                if (userNameEl) {
                    userNameEl.textContent = displayName;
                }

                const userEmailEl = document.getElementById('userEmail');
                if (userEmailEl) {
                    userEmailEl.textContent = user.email;
                }

                const userRoleEl = document.getElementById('userRole');
                if (userRoleEl) {
                    const role = String(user.role || '').toLowerCase();
                    const roleClass = role === 'admin' ? 'bg-danger' : 'bg-info';
                    userRoleEl.innerHTML = `<span class="badge ${roleClass}">${role.toUpperCase()}</span>`;
                }

                const adminSection = document.getElementById('adminSection');
                const userSection = document.getElementById('userSection');

                const isAdmin = String(user.role || '').toLowerCase() === 'admin';
                if (isAdmin) {
                    if (adminSection) adminSection.style.display = 'block';
                    if (userSection) userSection.style.display = 'none';
                } else {
                    if (adminSection) adminSection.style.display = 'none';
                    if (userSection) userSection.style.display = 'block';
                }

                const errorDiv = document.getElementById('dashboardError');
                if (errorDiv) {
                    errorDiv.style.display = 'none';
                }

                // Load skills and projects
                loadDashboardSkills(user.id);
                loadDashboardProjects(user.id);
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

// Load and render skills list in dashboard
function loadDashboardSkills(userId) {
    const container = document.getElementById('skillsList');
    if (!container) return;

    apiGetSkills(userId)
        .then(res => {
            const skills = Array.isArray(res) ? res : (res.skills || []);
            if (skills.length === 0) {
                container.innerHTML = '<p class="text-muted text-center mb-0">No skills added yet. Click "Add Skill" above.</p>';
                return;
            }

            // Group skills by category
            const grouped = { Frontend: [], Backend: [], Database: [], DevOps: [], Other: [] };
            skills.forEach(skill => {
                const cat = skill.category || 'Other';
                if (!grouped[cat]) grouped[cat] = [];
                grouped[cat].push(skill);
            });

            let html = '<div class="table-responsive"><table class="table table-sm table-hover mb-0"><thead><tr><th>Skill</th><th>Category</th><th>Proficiency</th><th>Action</th></tr></thead><tbody>';
            skills.forEach(skill => {
                const categoryIcons = {
                    'Frontend': '🎨',
                    'Backend': '⚙️',
                    'Database': '🗄️',
                    'DevOps': '🚀',
                    'Other': '📦'
                };
                const catIcon = categoryIcons[skill.category] || '📦';
                const catBadgeClass = skill.category === 'Frontend' ? 'bg-info' :
                    skill.category === 'Backend' ? 'bg-success' :
                        skill.category === 'Database' ? 'bg-warning text-dark' :
                            skill.category === 'DevOps' ? 'bg-danger' : 'bg-secondary';
                html += `<tr>
                    <td>${skill.name}</td>
                    <td><span class="badge ${catBadgeClass}">${catIcon} ${skill.category || 'Other'}</span></td>
                    <td><span class="badge bg-secondary">${skill.proficiency}</span></td>
                    <td><button class="btn btn-sm btn-outline-danger delete-skill-btn" data-id="${skill.id}"><i class="fas fa-trash"></i></button></td>
                </tr>`;
            });
            html += '</tbody></table></div>';
            container.innerHTML = html;
        })
        .catch(() => {
            container.innerHTML = '<p class="text-danger text-center mb-0">Failed to load skills.</p>';
        });
}

// Load and render projects list in dashboard
function loadDashboardProjects(userId) {
    const container = document.getElementById('projectsList');
    if (!container) return;

    apiGetProjects(userId)
        .then(res => {
            const projects = Array.isArray(res) ? res : (res.projects || []);
            if (projects.length === 0) {
                container.innerHTML = '<p class="text-muted text-center mb-0">No projects added yet. Click "Add Project" above.</p>';
                return;
            }

            let html = '<div class="table-responsive"><table class="table table-sm table-hover mb-0"><thead><tr><th>Title</th><th>Technologies</th><th>Links</th><th>Action</th></tr></thead><tbody>';
            projects.forEach(project => {
                const links = [];
                if (project.github_url) links.push(`<a href="${project.github_url}" target="_blank" class="me-1"><i class="fab fa-github"></i></a>`);
                if (project.project_url) links.push(`<a href="${project.project_url}" target="_blank"><i class="fas fa-external-link-alt"></i></a>`);

                // Parse technologies
                let techBadges = '-';
                if (project.technologies) {
                    const techs = project.technologies.split(',').map(t => t.trim()).filter(t => t);
                    const techColors = {
                        'PHP': 'bg-primary',
                        'MySQL': 'bg-info',
                        'JavaScript': 'bg-warning text-dark',
                        'HTML/CSS': 'bg-danger',
                        'React': 'bg-info',
                        'Node.js': 'bg-success',
                        'Python': 'bg-primary',
                        'Bootstrap': 'bg-purple',
                        'API': 'bg-secondary'
                    };
                    techBadges = techs.map(t => {
                        const colorClass = techColors[t] || 'bg-secondary';
                        const style = t === 'Bootstrap' ? 'style="background-color:#7952b3!important"' : '';
                        return `<span class="badge ${colorClass} me-1" ${style}>${t}</span>`;
                    }).join('');
                }

                html += `<tr>
                    <td><strong>${project.title}</strong><br><small class="text-muted">${project.description || ''}</small></td>
                    <td>${techBadges}</td>
                    <td>${links.join('') || '-'}</td>
                    <td><button class="btn btn-sm btn-outline-danger delete-project-btn" data-id="${project.id}"><i class="fas fa-trash"></i></button></td>
                </tr>`;
            });
            html += '</tbody></table></div>';
            container.innerHTML = html;
        })
        .catch(() => {
            container.innerHTML = '<p class="text-danger text-center mb-0">Failed to load projects.</p>';
        });
}

// Add skill form handler
document.addEventListener('submit', function (e) {
    if (e.target && e.target.id === 'skillForm') {
        e.preventDefault();
        if (!dashboardUserId) return;

        const name = document.getElementById('skillName').value.trim();
        const proficiency = document.getElementById('skillProficiency').value;
        const category = document.getElementById('skillCategory').value;

        if (!name) return;

        // Block UI if available
        if (window.$ && $.blockUI) $.blockUI({ message: '<h3>Adding skill...</h3>' });

        apiAddSkill(dashboardUserId, { name, proficiency, category })
            .then(res => {
                if (window.$ && $.unblockUI) $.unblockUI();
                if (res.success) {
                    if (window.toastr) toastr.success('Skill added successfully!');
                    document.getElementById('skillForm').reset();
                    loadDashboardSkills(dashboardUserId);
                } else {
                    if (window.toastr) toastr.error(res.message || 'Failed to add skill');
                    else alert(res.message || 'Failed to add skill');
                }
            })
            .catch(() => {
                if (window.$ && $.unblockUI) $.unblockUI();
                if (window.toastr) toastr.error('Failed to add skill');
                else alert('Failed to add skill');
            });
    }
});

// Add project form handler
document.addEventListener('submit', function (e) {
    if (e.target && e.target.id === 'projectForm') {
        e.preventDefault();
        if (!dashboardUserId) return;

        const title = document.getElementById('projectTitle').value.trim();
        const description = document.getElementById('projectDesc').value.trim();
        const github_url = document.getElementById('projectGithub').value.trim();
        const project_url = document.getElementById('projectUrl').value.trim();

        // Collect selected technologies
        const techCheckboxes = document.querySelectorAll('.tech-checkbox:checked');
        const technologies = Array.from(techCheckboxes).map(cb => cb.value).join(',');

        if (!title) return;

        // Block UI if available
        if (window.$ && $.blockUI) $.blockUI({ message: '<h3>Adding project...</h3>' });

        apiAddProject(dashboardUserId, { title, description, github_url, project_url, technologies })
            .then(res => {
                if (window.$ && $.unblockUI) $.unblockUI();
                if (res.success) {
                    if (window.toastr) toastr.success('Project added successfully!');
                    document.getElementById('projectForm').reset();
                    // Uncheck all tech checkboxes
                    document.querySelectorAll('.tech-checkbox').forEach(cb => cb.checked = false);
                    loadDashboardProjects(dashboardUserId);
                } else {
                    if (window.toastr) toastr.error(res.message || 'Failed to add project');
                    else alert(res.message || 'Failed to add project');
                }
            })
            .catch(() => {
                if (window.$ && $.unblockUI) $.unblockUI();
                if (window.toastr) toastr.error('Failed to add project');
                else alert('Failed to add project');
            });
    }
});

// Delete skill handler
document.addEventListener('click', function (e) {
    const btn = e.target.closest('.delete-skill-btn');
    if (!btn || !dashboardUserId) return;

    const skillId = btn.dataset.id;
    if (!confirm('Delete this skill?')) return;

    if (window.$ && $.blockUI) $.blockUI({ message: '<h3>Deleting...</h3>' });

    apiDeleteSkill(dashboardUserId, skillId)
        .then(res => {
            if (window.$ && $.unblockUI) $.unblockUI();
            if (res.success) {
                if (window.toastr) toastr.success('Skill deleted!');
                loadDashboardSkills(dashboardUserId);
            } else {
                if (window.toastr) toastr.error(res.message || 'Failed to delete skill');
                else alert(res.message || 'Failed to delete skill');
            }
        })
        .catch(() => {
            if (window.$ && $.unblockUI) $.unblockUI();
            if (window.toastr) toastr.error('Failed to delete skill');
            else alert('Failed to delete skill');
        });
});

// Delete project handler
document.addEventListener('click', function (e) {
    const btn = e.target.closest('.delete-project-btn');
    if (!btn || !dashboardUserId) return;

    const projectId = btn.dataset.id;
    if (!confirm('Delete this project?')) return;

    if (window.$ && $.blockUI) $.blockUI({ message: '<h3>Deleting...</h3>' });

    apiDeleteProject(dashboardUserId, projectId)
        .then(res => {
            if (window.$ && $.unblockUI) $.unblockUI();
            if (res.success) {
                if (window.toastr) toastr.success('Project deleted!');
                loadDashboardProjects(dashboardUserId);
            } else {
                if (window.toastr) toastr.error(res.message || 'Failed to delete project');
                else alert(res.message || 'Failed to delete project');
            }
        })
        .catch(() => {
            if (window.$ && $.unblockUI) $.unblockUI();
            if (window.toastr) toastr.error('Failed to delete project');
            else alert('Failed to delete project');
        });
});
