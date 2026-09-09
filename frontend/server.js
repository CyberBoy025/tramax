// Entry point for cPanel's "Setup Node.js App" (Phusion Passenger).
// Passenger runs Node apps by requiring an explicit file that listens on
// process.env.PORT — it has no way to invoke an npm script like
// `next start`, so this wraps Next.js's programmatic API to produce that
// entry point. Only used in that deployment path; local dev/build still
// use the normal `next dev` / `next build` scripts.
const { createServer } = require("node:http");
const next = require("next");

const port = process.env.PORT || 3000;
const app = next({ dev: false });
const handle = app.getRequestHandler();

app.prepare().then(() => {
  createServer((req, res) => handle(req, res)).listen(port, () => {
    console.log(`Tramax frontend listening on port ${port}`);
  });
});
