// Override jQuery.fn.load for spapp to use PHP loader
const originalLoad = jQuery.fn.load;
jQuery.fn.load = function (url, ...args) {
  if (url && url.endsWith('.html') && url.startsWith('views/')) {
    const view = url.replace('views/', '');
    return this.html('<div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>').load('loader.php?view=' + encodeURIComponent(view), ...args);
  }
  return originalLoad.call(this, url, ...args);
};
