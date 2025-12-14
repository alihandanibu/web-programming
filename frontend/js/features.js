// Load projects dynamically
function loadProjects() {
  getAllProjects()
    .then(response => {
      if (response.data && Array.isArray(response.data)) {
        const projectsContainer = $('#projects .row');
        projectsContainer.empty();

        response.data.forEach(project => {
          const technologies = project.technologies ? project.technologies.split(',').map(t => {
            return '<span class="badge bg-primary">' + t.trim() + '</span>';
          }).join(' ') : '';

          const html = `
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card card-hover h-100">
                                <div class="card-body">
                                    <h5 class="card-title">${project.title}</h5>
                                    <p class="card-text">${project.description || 'No description'}</p>
                                    <div class="technologies mb-3">${technologies}</div>
                                    <div class="project-links">
                                        ${project.github_url ? `<a href="${project.github_url}" class="btn btn-sm btn-outline-primary" target="_blank"><i class="fab fa-github me-1"></i> GitHub</a>` : ''}
                                        ${project.link ? `<a href="${project.link}" class="btn btn-sm btn-outline-secondary" target="_blank"><i class="fas fa-external-link-alt me-1"></i> Live</a>` : ''}
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
          projectsContainer.append(html);
        });
      }
    })
    .catch(error => {
      console.log('Could not load projects from API, using static data');
    });
}

// Load skills dynamically
function loadSkills() {
  getAllSkills()
    .then(response => {
      if (response.data && Array.isArray(response.data)) {
        const skillsContainer = $('#skills .skills-grid');
        if (skillsContainer.length > 0) {
          skillsContainer.empty();

          response.data.forEach(skill => {
            const html = `
                            <div class="skill-item">
                                <h5>${skill.name}</h5>
                                <div class="skill-bar">
                                    <div class="skill-progress" style="width: 80%"></div>
                                </div>
                                <p class="text-muted">${skill.proficiency}</p>
                            </div>
                        `;
            skillsContainer.append(html);
          });
        }
      }
    })
    .catch(error => {
      console.log('Could not load skills from API, using static data');
    });
}

// Load experiences dynamically
function loadExperiences() {
  getAllExperiences()
    .then(response => {
      if (response.data && Array.isArray(response.data)) {
        const experiencesContainer = $('#about .experiences-container');
        if (experiencesContainer.length > 0) {
          experiencesContainer.empty();

          response.data.forEach(exp => {
            const html = `
                            <div class="experience-item mb-4">
                                <h5>${exp.title}</h5>
                                <p class="text-muted">${exp.company}</p>
                                <p>${exp.description}</p>
                            </div>
                        `;
            experiencesContainer.append(html);
          });
        }
      }
    })
    .catch(error => {
      console.log('Could not load experiences from API, using static data');
    });
}

// Load data when sections are viewed
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
