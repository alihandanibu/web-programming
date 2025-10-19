// Handan Portfolio – SPApp Setup
$(document).ready(function () {
  // Initialize the SPApp router
  var app = $.spapp({
    pageNotFound: 'error_404', // fallback section
    templateDir: 'views/'      // where your html templates live
  });

  // Define routes (each matches a section id in index.html)
  app.route({ view: 'home',     load: 'home.html' });
  app.route({ view: 'about',    load: 'about.html' });
  app.route({ view: 'projects', load: 'projects.html' });
  app.route({ view: 'skills',   load: 'skills.html' });
  app.route({ view: 'contact',  load: 'contact.html' });
  app.route({ view: 'login',    load: 'login.html' });
  app.route({ view: 'register', load: 'register.html' });
  app.route({ view: 'error_404',load: '404.html' });

  // Default hash route
  if (!window.location.hash) {
    window.location.hash = '#home';
  }

  // Start the app
  app.run();

  // ---- UI helpers ----

  // Show only current section to prevent stacking
  function showOnlyTarget() {
    var target = window.location.hash ? window.location.hash.substring(1) : 'home';
    $('#spapp > section').hide();
    $('#' + target).show();
  }
  showOnlyTarget();
  $(window).on('hashchange', showOnlyTarget);
  $(document).on('spapp:ready', showOnlyTarget);

  // Active nav link highlight
  function setActiveLink() {
    var hash = window.location.hash || '#home';
    $('.nav-link').removeClass('active');
    $('.nav-link[href="' + hash + '"]').addClass('active');
  }
  setActiveLink();
  $(window).on('hashchange', setActiveLink);

  // Simple form alerts (for static milestone)
  $(document).on('submit', '#contactForm', function (e) {
    e.preventDefault();
    alert('Thank you! Your message has been sent.');
    this.reset();
  });
  $(document).on('submit', '#loginForm', function (e) {
    e.preventDefault();
    alert('Login will be implemented later.');
    this.reset();
  });
  $(document).on('submit', '#registerForm', function (e) {
    e.preventDefault();
    alert('Registration will be implemented later.');
    this.reset();
  });
});