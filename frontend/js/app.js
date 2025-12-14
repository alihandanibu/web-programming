// Portfolio SPA Application
$(document).ready(function () {

  // Get user from localStorage
  function getUser() {
    const u = localStorage.getItem('user');
    return u ? JSON.parse(u) : null;
  }

  function isAuthenticated() {
    return !!localStorage.getItem('token');
  }

  // Initialize SPA router
  const app = $.spapp({
    pageNotFound: 'error_404',
    templateDir: 'views/',
    defaultView: 'home'
  });

  // Public routes
  app.route({ view: 'home', load: 'home.html' });
  app.route({ view: 'about', load: 'about.html' });
  app.route({ view: 'projects', load: 'projects.html' });
  app.route({ view: 'skills', load: 'skills.html' });
  app.route({ view: 'contact', load: 'contact.html' });
  app.route({ view: 'login', load: 'login.html' });
  app.route({ view: 'register', load: 'register.html' });
  app.route({ view: 'error_404', load: 'error_404.html' });

  // Protected route - requires login
  app.route({
    view: 'dashboard',
    load: 'dashboard.html',
    onCreate: function () {
      if (!isAuthenticated()) {
        window.location.hash = '#login';
      }
    }
  });

  // Admin only route
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

  // ===== DEFAULT ROUTE =====
  if (!window.location.hash) {
    window.location.hash = '#home';
  }

  app.run();

  // =====================================================
  // ================= AUTH HANDLERS =====================
  // =====================================================

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

    // Password match validation
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

  // LOGOUT (ako ima dugme)
  $(document).on('click', '#logoutBtn', function () {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    window.location.hash = '#home';
  });

});
