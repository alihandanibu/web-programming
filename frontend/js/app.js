$(document).ready(function () {

  // Initialize toastr options
  if (window.toastr) {
    toastr.options = {
      closeButton: true,
      progressBar: true,
      positionClass: 'toast-top-right',
      timeOut: 3000
    };
  }

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

    const role = String(user?.role || '').toLowerCase();
    const isAdmin = role === 'admin';

    const dashboardNav = document.getElementById('dashboardNav');
    const adminNav = document.getElementById('adminNav');
    const logoutNav = document.getElementById('logoutNav');

    if (dashboardNav) {
      dashboardNav.style.display = authed ? '' : 'none';
    }
    if (adminNav) {
      adminNav.style.display = (authed && isAdmin) ? '' : 'none';
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

  // Initialize form validations when view is loaded
  function initializeValidations(viewId) {
    if (!$.fn.validate) return;

    const validationConfig = {
      errorElement: 'div',
      errorClass: 'invalid-feedback',
      highlight: function (element) {
        $(element).addClass('is-invalid').removeClass('is-valid');
      },
      unhighlight: function (element) {
        $(element).removeClass('is-invalid').addClass('is-valid');
      },
      errorPlacement: function (error, element) {
        if (element.closest('.input-group').length) {
          error.insertAfter(element.closest('.input-group'));
        } else {
          error.insertAfter(element);
        }
      }
    };

    if (viewId === 'login') {
      $('#loginForm').validate({
        ...validationConfig,
        rules: {
          email: { required: true, email: true },
          password: { required: true, minlength: 6 }
        },
        messages: {
          email: { required: 'Email is required.', email: 'Please enter a valid email.' },
          password: { required: 'Password is required.', minlength: 'Minimum 6 characters.' }
        }
      });
    }

    if (viewId === 'register') {
      $('#registerForm').validate({
        ...validationConfig,
        rules: {
          name: { required: true, minlength: 2 },
          email: { required: true, email: true },
          password: { required: true, minlength: 8, maxlength: 20 },
          confirm_password: { required: true, equalTo: '#registerPassword' }
        },
        messages: {
          name: { required: 'Name is required.', minlength: 'Minimum 2 characters.' },
          email: { required: 'Email is required.', email: 'Please enter a valid email.' },
          password: { required: 'Password is required.', minlength: 'Minimum 8 characters.', maxlength: 'Maximum 20 characters.' },
          confirm_password: { required: 'Confirm password.', equalTo: 'Passwords do not match.' }
        }
      });
    }

    if (viewId === 'contact') {
      $('#contactForm').validate({
        ...validationConfig,
        rules: {
          name: { required: true, minlength: 2 },
          email: { required: true, email: true },
          subject: { required: true, minlength: 3 },
          message: { required: true, minlength: 10 }
        },
        messages: {
          name: 'Please enter your name (min 2 chars).',
          email: { required: 'Email is required.', email: 'Please enter a valid email.' },
          subject: 'Subject is required (min 3 chars).',
          message: 'Message must be at least 10 characters.'
        }
      });
    }

    if (viewId === 'dashboard') {
      $('#skillForm').validate({
        ...validationConfig,
        rules: {
          name: { required: true, minlength: 2 },
          proficiency: { required: true }
        },
        messages: {
          name: 'Skill name is required.',
          proficiency: 'Select proficiency.'
        }
      });

      $('#projectForm').validate({
        ...validationConfig,
        rules: {
          title: { required: true, minlength: 3 },
          github_url: { url: true },
          project_url: { url: true }
        },
        messages: {
          title: 'Project title is required (min 3 chars).',
          github_url: 'Please enter a valid URL.',
          project_url: 'Please enter a valid URL.'
        }
      });
    }
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

    // Initialize validations after view is loaded
    setTimeout(() => initializeValidations(view), 100);
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
      const role = String(user?.role || '').toLowerCase();
      if (!isAuthenticated() || role !== 'admin') {
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

    // Check if form is valid (jQuery Validation)
    if ($.fn.validate && !$(this).valid()) return;

    const email = $('#loginEmail').val();
    const password = $('#loginPassword').val();
    const alertBox = $('#loginAlert');

    // Block UI during request
    if ($.blockUI) $.blockUI({ message: '<h3>Logging in...</h3>' });

    apiLogin(email, password).then(res => {
      if ($.unblockUI) $.unblockUI();

      if (!res.success) {
        if (window.toastr) toastr.error(res.message || 'Login failed');
        alertBox
          .removeClass('d-none alert-success')
          .addClass('alert alert-danger')
          .text(res.message || 'Login failed');
        return;
      }

      localStorage.setItem('token', res.token);
      localStorage.setItem('user', JSON.stringify(res.user));

      if (window.toastr) toastr.success('Login successful!');
      alertBox
        .removeClass('d-none alert-danger')
        .addClass('alert alert-success')
        .text('Login successful. Redirecting...');

      setTimeout(() => {
        window.location.hash = '#dashboard';
      }, 800);
    }).catch(err => {
      if ($.unblockUI) $.unblockUI();
      if (window.toastr) toastr.error('Login failed');
    });
  });

  // REGISTER
  $(document).on('submit', '#registerForm', function (e) {
    e.preventDefault();

    // Check if form is valid (jQuery Validation)
    if ($.fn.validate && !$(this).valid()) return;

    const name = $('#registerName').val();
    const email = $('#registerEmail').val();
    const password = $('#registerPassword').val();
    const confirm = $('#registerConfirmPassword').val();
    const alertBox = $('#registerAlert');

    if (password !== confirm) {
      if (window.toastr) toastr.error('Passwords do not match');
      alertBox
        .removeClass('d-none alert-success')
        .addClass('alert alert-danger')
        .text('Passwords do not match');
      return;
    }

    const data = { name, email, password };

    // Block UI during request
    if ($.blockUI) $.blockUI({ message: '<h3>Registering...</h3>' });

    apiRegister(data).then(res => {
      if ($.unblockUI) $.unblockUI();

      if (!res.success) {
        if (window.toastr) toastr.error(res.message || 'Registration failed');
        alertBox
          .removeClass('d-none alert-success')
          .addClass('alert alert-danger')
          .text(res.message || 'Registration failed');
        return;
      }

      if (window.toastr) toastr.success('Registration successful!');
      alertBox
        .removeClass('d-none alert-danger')
        .addClass('alert alert-success')
        .text('Registration successful. You can now login.');

      setTimeout(() => {
        window.location.hash = '#login';
      }, 800);
    }).catch(err => {
      if ($.unblockUI) $.unblockUI();
      if (window.toastr) toastr.error('Registration failed');
    });
  });

  // LOGOUT
  $(document).on('click', '#logoutBtn', function () {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    if (window.toastr) toastr.info('Logged out successfully');
    window.location.hash = '#home';
  });

  // NAV LOGOUT
  $(document).on('click', '#navLogoutBtn', function (e) {
    e.preventDefault();
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    if (window.toastr) toastr.info('Logged out successfully');
    window.location.hash = '#home';
  });

});
