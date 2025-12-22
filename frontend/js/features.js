function loadProjects() {
  const token = localStorage.getItem('token');
  if (!token) {
    // No token: show static, hide dynamic
    $('#static-projects-content').removeClass('d-none');
    $('#user-projects-container').addClass('d-none').empty();
    return;
  }

  // Verify token and get userId
  apiVerifyToken()
    .then(res => {
      if (!res.valid) throw new Error('invalid');
      return res.user.user_id;
    })
    .then(userId => apiGetProjects(userId))
    .then(res => {
      const projects = Array.isArray(res) ? res : (res.projects || []);

      // Hide static, show dynamic container
      $('#static-projects-content').addClass('d-none');
      const $container = $('#user-projects-container').removeClass('d-none').empty();

      if (projects.length === 0) {
        $container.html('<div class="col-12 text-center py-4"><p class="text-muted mb-3">No projects yet.</p><a href="#dashboard" class="btn btn-primary" onclick="setTimeout(function(){document.getElementById(\'projectsSection\')?.scrollIntoView({behavior:\'smooth\'});},300);"><i class="fas fa-plus me-2"></i>Add Project from Dashboard</a></div>');
        return;
      }

      projects.forEach(project => {
        $container.append(`
          <div class="col-lg-4 col-md-6">
            <div class="card card-hover h-100">
              <div class="card-body">
                <h5 class="card-title">${project.title}</h5>
                <p class="card-text">${project.description || 'No description'}</p>
                <div class="project-links d-flex gap-2 flex-wrap">
                  ${project.github_url ? `<a href="${project.github_url}" class="btn btn-sm btn-outline-primary" target="_blank"><i class="fab fa-github me-1"></i>GitHub</a>` : ''}
                  ${(project.project_url || project.link) ? `<a href="${project.project_url || project.link}" class="btn btn-sm btn-outline-secondary" target="_blank"><i class="fas fa-external-link-alt me-1"></i>Live</a>` : ''}
                </div>
              </div>
            </div>
          </div>
        `);
      });
    })
    .catch(() => {
      // On error, fallback to static
      $('#static-projects-content').removeClass('d-none');
      $('#user-projects-container').addClass('d-none').empty();
    });
}

function loadSkills() {
  const token = localStorage.getItem('token');
  if (!token) {
    // No token: show static, hide dynamic
    $('#static-skills-content').removeClass('d-none');
    $('#user-skills-container').addClass('d-none').empty();
    return;
  }

  // Verify token and get userId
  apiVerifyToken()
    .then(res => {
      if (!res.valid) throw new Error('invalid');
      return res.user.user_id;
    })
    .then(userId => apiGetSkills(userId))
    .then(res => {
      const skills = Array.isArray(res) ? res : (res.skills || []);

      // Hide static, show dynamic container
      $('#static-skills-content').addClass('d-none');
      const $container = $('#user-skills-container').removeClass('d-none').empty();

      if (skills.length === 0) {
        $container.html('<div class="col-12 text-center py-4"><p class="text-muted mb-3">No skills yet.</p><a href="#dashboard" class="btn btn-primary" onclick="setTimeout(function(){document.getElementById(\'skillsSection\')?.scrollIntoView({behavior:\'smooth\'});},300);"><i class="fas fa-plus me-2"></i>Add Skill from Dashboard</a></div>');
        return;
      }

      const proficiencyToWidth = (p) => {
        const v = String(p || '').toLowerCase();
        if (v === 'beginner') return 35;
        if (v === 'intermediate') return 60;
        if (v === 'advanced') return 80;
        if (v === 'expert') return 95;
        return 60;
      };

      skills.forEach(skill => {
        const width = proficiencyToWidth(skill.proficiency);
        $container.append(`
          <div class="col-lg-6 mb-3">
            <div class="bg-white border rounded-3 p-3">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <strong>${skill.name}</strong>
                <small class="text-muted text-uppercase">${skill.proficiency}</small>
              </div>
              <div class="skill-track">
                <div class="skill-fill" data-width="${width}"></div>
              </div>
            </div>
          </div>
        `);
      });

      // Animate skill bars
      $('#user-skills-container .skill-fill').css('width', '0%');
      setTimeout(() => {
        $('#user-skills-container .skill-fill').each(function () {
          $(this).css('width', $(this).data('width') + '%');
        });
      }, 50);
    })
    .catch(() => {
      // On error, fallback to static
      $('#static-skills-content').removeClass('d-none');
      $('#user-skills-container').addClass('d-none').empty();
    });
}

function loadExperiences() {
  const user = localStorage.getItem('user');
  if (!user || !localStorage.getItem('token')) {
    return;
  }

  const userId = JSON.parse(user).id;
  if (!userId) {
    return;
  }

  apiGetExperiences(userId)
    .then(res => {
      const experiences = Array.isArray(res) ? res : (res.experiences || []);
      if (!Array.isArray(experiences) || experiences.length === 0) {
        return;
      }

      const experiencesContainer = $('#about .experiences-container');
      if (experiencesContainer.length === 0) {
        return;
      }

      experiencesContainer.empty();

      experiences.forEach(exp => {
        const html = `
          <div class="experience-item mb-4">
            <h5 class="mb-1">${exp.position || ''}</h5>
            <p class="text-muted mb-2">${exp.company || ''}</p>
            ${exp.description ? `<p class="mb-0">${exp.description}</p>` : ''}
          </div>
        `;
        experiencesContainer.append(html);
      });
    })
    .catch(() => {
    });
}

$(document).on('spapp:ready', function () {
  const currentHash = window.location.hash.substring(1) || 'home';

  if (currentHash === 'projects') {
    loadProjects();
  } else if (currentHash === 'skills') {
    loadSkills();
  } else if (currentHash === 'about') {
    loadExperiences();
  }
});

$(window).on('hashchange', function () {
  const currentHash = window.location.hash.substring(1);

  if (currentHash === 'projects') {
    loadProjects();
  } else if (currentHash === 'skills') {
    loadSkills();
  } else if (currentHash === 'about') {
    loadExperiences();
  }
});

$(document).on('submit', '#contactForm', function (e) {
  e.preventDefault();

  const $form = $(this);
  const subject = String($('#subject').val() || '').trim();
  const message = String($('#message').val() || '').trim();

  const payload = {
    name: String($('#name').val() || '').trim(),
    email: String($('#email').val() || '').trim(),
    // Map subject into message
    message: subject ? (`Subject: ${subject}\n\n${message}`) : message
  };

  let $alert = $form.find('.contact-alert');
  if ($alert.length === 0) {
    $alert = $('<div class="alert d-none contact-alert mt-3" role="alert"></div>');
    $form.append($alert);
  }

  $alert.removeClass('d-none alert-success alert-danger').addClass('alert alert-info').text('Sending...');

  apiSendContact(payload)
    .then(res => {
      if (res && res.success) {
        $alert.removeClass('alert-info alert-danger').addClass('alert-success').text('Message sent successfully.');
        $form[0].reset();
        return;
      }
      $alert.removeClass('alert-info alert-success').addClass('alert-danger').text((res && res.message) ? res.message : 'Failed to send message.');
    })
    .catch(() => {
      $alert.removeClass('alert-info alert-success').addClass('alert-danger').text('Failed to send message.');
    });
});

// Navbar logout handler
$(document).on('click', '#navLogoutBtn', function (e) {
  e.preventDefault();
  localStorage.removeItem('token');
  localStorage.removeItem('user');
  window.location.hash = '#home';
  location.reload();
});

// Pre-fill contact form with logged-in user info
function prefillContactForm() {
  const token = localStorage.getItem('token');
  const userStr = localStorage.getItem('user');
  if (!token || !userStr) return;

  try {
    const user = JSON.parse(userStr);
    const $name = $('#contactForm #name');
    const $email = $('#contactForm #email');
    if ($name.length && !$name.val()) $name.val(user.name || '');
    if ($email.length && !$email.val()) $email.val(user.email || '');
  } catch (e) { }
}

$(window).on('hashchange', function () {
  if (window.location.hash === '#contact') {
    setTimeout(prefillContactForm, 100);
  }
});

$(document).on('spapp:ready', function () {
  if (window.location.hash === '#contact') {
    setTimeout(prefillContactForm, 100);
  }
});
