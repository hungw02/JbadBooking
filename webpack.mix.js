const mix = require("laravel-mix");

// Thêm PostCSS và TailwindCSS
mix.js("resources/js/app.js", "public/js")
   .sass("resources/scss/app.scss", "public/css")
   .postCss("resources/css/app.css", "public/css", [
      require("tailwindcss"),
      require("autoprefixer"),
   ])
   .sourceMaps()
   .browserSync({
      proxy: "localhost:8000",
      files: [
         "resources/views/**/*.blade.php",
         "public/css/**/*.css",
         "public/js/**/*.js",
      ],
   })
   .options({
      processCssUrls: false,
      sassOptions: {
         includePaths: ['resources/scss']
      }
   })
   .disableNotifications();

// Xóa các module files cũ trong public directory
if (mix.inProduction()) {
   mix.version();
}
