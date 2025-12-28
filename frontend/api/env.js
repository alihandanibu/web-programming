export default function handler(req, res) {
  // Vercel environment variables are available server-side (Serverless Function runtime)
  // Expose ONLY the API base URL to the browser.
  const apiUrl =
    process.env.VITE_API_URL ||
    process.env.NEXT_PUBLIC_API_URL ||
    process.env.REACT_APP_API_URL ||
    process.env.API_URL ||
    '';

  res.setHeader('Content-Type', 'application/javascript; charset=utf-8');
  res.setHeader('Cache-Control', 'no-store');

  // Keep it simple and safe: stringify to avoid injection.
  res.status(200).send(
    `window.__ENV = Object.assign({}, window.__ENV || {}, { API_URL: ${JSON.stringify(
      apiUrl
    )} });`
  );
}
