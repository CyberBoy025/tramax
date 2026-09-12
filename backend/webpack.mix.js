const mix = require("laravel-mix");

// Laravel Mix, not Vite — matching the UltrAdemy reference stack's asset
// pipeline (see Conversion-README.md §5/§9/§19). Node/npm is a build-time
// dependency only: this compiles resources/{sass,js} into public/{css,js}
// once, and Laravel/Apache serves the compiled output from then on — no
// persistent Node process in production.
mix
  .js("resources/js/app.js", "public/js")
  .sass("resources/sass/app.scss", "public/css")
  .options({ processCssUrls: false })
  .version();
