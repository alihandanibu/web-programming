export default function handler(req, res) {
  // Expose ONLY the API base URL to the browser.
  const apiUrl =
    process.env.VITE_API_URL ||
    process.env.NEXT_PUBLIC_API_URL ||
    process.env.REACT_APP_API_URL ||
    process.env.API_URL ||
    '';

  res.setHeader('Content-Type', 'application/javascript; charset=utf-8');
  res.setHeader('Cache-Control', 'no-store');

  res
    .status(200)
    .send(
      `window.__ENV = Object.assign({}, window.__ENV || {}, { API_URL: ${JSON.stringify(apiUrl)} });`
    );
}
