/** @type {import('tailwindcss').Config} */
module.exports = {
  corePlugins: {
    preflight: false, // ❌ Відключає @tailwind base повністю
  },
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue"
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}
