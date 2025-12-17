$(document).ready(function () {

  function getUser() {
    const u = localStorage.getItem('user');
    return u ? JSON.parse(u) : null;
  }

  function isAuthenticated() {
    return !!localStorage.getItem('token');
  }

  function getCurrentView() {
    const raw = (window.location.hash || '').trim();
    const view = raw.replace(/^#/, '').replace(/^\/+/, '').split('?')[0].trim();
    return view || 'home';
  }

  function setActiveNav(view) {
    $('.navbar-nav .nav-link').removeClass('active');
    const $link = $('.navbar-nav .nav-link[href="#' + view + '"]');
    if ($link.length) {
      $link.addClass('active');
    }
  }

  function updateAuthNav() {
    const user = getUser();
    const authed = isAuthenticated();

    const dashboardNav = document.getElementById('dashboardNav');
    const adminNav = document.getElementById('adminNav');
    const logoutNav = document.getElementById('logoutNav');

    if (dashboardNav) {
      dashboardNav.style.display = authed ? '' : 'none';
    }
    if (adminNav) {
      adminNav.style.display = (authed && user && user.role === 'admin') ? '' : 'none';
    }
    if (logoutNav) {
      logoutNav.style.display = authed ? '' : 'none';
    }

    // Auth UI: login/register vs dashboard/admin/logout
    const loginLink = document.querySelector('.navbar-nav .nav-link[href="#login"]');
    const registerLink = document.querySelector('.navbar-nav .nav-link[href="#register"]');
    if (loginLink && loginLink.parentElement) {
      loginLink.parentElement.style.display = authed ? 'none' : '';
    }
    if (registerLink && registerLink.parentElement) {
      registerLink.parentElement.style.display = authed ? 'none' : '';
    }
  }

  function animateSkillBars() {
    $('#skills .skill-fill').each(function () {
      $(this).css('width', '0%');
    });

    setTimeout(() => {
      $('#skills .skill-fill').each(function () {
        const width = Number($(this).data('width')) || 0;
        $(this).css('width', width + '%');
      });
    }, 50);
  }

  function syncSpaUi() {
    const view = getCurrentView();

    document.body.classList.add('spa-ready');

    $('.spa-view').removeClass('is-active');

    const $target = $('#' + view);
    if ($target.length) {
      $target.addClass('is-active');
    } else {
      $('#error_404').addClass('is-active');
    }

    setActiveNav(view);
    updateAuthNav();

    if (view === 'skills') {
      animateSkillBars();
    }
  }

  const app = $.spapp({
    pageNotFound: 'error_404',
    templateDir: 'views/',
    defaultView: 'home'
  });

  app.route({ view: 'home', load: 'home.html' });
  app.route({ view: 'about', load: 'about.html' });
  app.route({ view: 'projects', load: 'projects.html' });
  app.route({ view: 'skills', load: 'skills.html' });
  app.route({ view: 'contact', load: 'contact.html' });
  app.route({ view: 'login', load: 'login.html' });
  app.route({ view: 'register', load: 'register.html' });
  app.route({ view: 'error_404', load: 'error_404.html' });

  // Auth required
  app.route({
    view: 'dashboard',
    load: 'dashboard.html',
    onCreate: function () {
      if (!isAuthenticated()) {
        window.location.hash = '#login';
      }
    }
  });

  // Admin only
  app.route({
    view: 'admin',
    load: 'admin.html',
    onCreate: function () {
      const user = getUser();
      if (!isAuthenticated() || user?.role !== 'admin') {
        window.location.hash = '#home';
      }
    }
  });

  if (!window.location.hash) {
    window.location.hash = '#home';
  }

  app.run();

  syncSpaUi();
  window.addEventListener('hashchange', syncSpaUi);

  // LOGIN
  $(document).on('submit', '#loginForm', function (e) {
    e.preventDefault();

    const email = $('#loginEmail').val();
    const password = $('#loginPassword').val();
    const alertBox = $('#loginAlert');

    apiLogin(email, password).then(res => {
      if (!res.success) {
        alertBox
          .removeClass('d-none alert-success')
          .addClass('alert alert-danger')
          .text(res.message || 'Login failed');
        return;
      }

      localStorage.setItem('token', res.token);
      localStorage.setItem('user', JSON.stringify(res.user));

      alertBox
        .removeClass('d-none alert-danger')
        .addClass('alert alert-success')
        .text('Login successful. Redirecting...');

      setTimeout(() => {
        window.location.hash = '#dashboard';
      }, 800);
    });
  });

  // REGISTER
  $(document).on('submit', '#registerForm', function (e) {
    e.preventDefault();

    const name = $('#registerName').val();
    const email = $('#registerEmail').val();
    const password = $('#registerPassword').val();
    const confirm = $('#registerConfirmPassword').val();
    const alertBox = $('#registerAlert');

    if (password !== confirm) {
      alertBox
        .removeClass('d-none alert-success')
        .addClass('alert alert-danger')
        .text('Passwords do not match');
      return;
    }

    const data = { name, email, password };

    apiRegister(data).then(res => {
      if (!res.success) {
        alertBox
          .removeClass('d-none alert-success')
          .addClass('alert alert-danger')
          .text(res.message || 'Registration failed');
        return;
      }

      alertBox
        .removeClass('d-none alert-danger')
        .addClass('alert alert-success')
        .text('Registration successful. You can now login.');

      setTimeout(() => {
        window.location.hash = '#login';
      }, 800);
    });
  });

  // LOGOUT
  $(document).on('click', '#logoutBtn', function () {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    window.location.hash = '#home';
  });

});
