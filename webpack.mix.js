const mix = require("laravel-mix");
require("laravel-mix-polyfill");

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

let extractible = [
  "axios",
  "bootstrap",
  "react",
  "redux",
  "jquery",
  "query-string",
  "react-dom",
  "@material-ui/core",
  "@material-ui/pickers",
  "react-icons",
  "react-moment",
  "react-naira",
  "react-paystack",
  "react-redux",
  "react-router-dom",
  "react-toastify",
  "uuid",
  "web-vitals",
  "html-react-parser",
  "moment",
  "react-modal",
  "redux-thunk",
];

if (mix.inProduction()) {
  mix.options({
    terser: {
      terserOptions: {
        compress: {
          drop_console: true,
        },
      },
    },
  });
}
mix
  .react("resources/js/user.js", "public/js/user.min.js")
  // .react("resources/js/superadmin.js", "public/js/secured_admin.min.js")
  // .js("resources/js/messenger.js", "public/js/messenger.min.js")
  .extract(extractible)
  .polyfill({
    enabled: true,
    useBuiltIns: "usage",
    // targets: { firefox: "40", ie: 11, safari: "9" },
    targets: ">0.2%,not dead,not op_mini all",
    corejs: 3,
    debug: false,
  })
  // .webpackConfig({
  //   node: {
  //     fs: "empty",
  //     child_process: "empty",
  //     net: "empty",
  //     tls: "empty"
  //   }
  // })
  .version()
  .browserSync("localhost:8033");
mix.disableNotifications();

// console.log(mix.config.babel());
