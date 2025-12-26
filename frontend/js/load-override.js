// Override jQuery.fn.load for SPAPP to use PHP loader (XAMPP only).
// On static hosts like Vercel, PHP won't execute, so we must load views directly.
(function () {
  const hostname = (window.location && window.location.hostname) ? String(window.location.hostname) : '';
  const isLocal = hostname === 'localhost' || hostname === '127.0.0.1';

  if (!isLocal) {
    return;
  }

  const originalLoad = jQuery.fn.load;
  jQuery.fn.load = function (url, ...args) {
    if (url && url.endsWith('.html') && url.startsWith('views/')) {
      const view = url.replace('views/', '');
      return this
        .html('<div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>')
        .load('loader.php?view=' + encodeURIComponent(view), ...args);
    }
    return originalLoad.call(this, url, ...args);
  };
})();
