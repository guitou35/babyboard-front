/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
    "./node_modules/flowbite/**/*.js",
    "./vendor/tales-from-a-dev/flowbite-bundle/templates/**/*.html.twig"
  ],
  theme: {
    extend: {
      zIndex: {
        '-10': '-10',
        '-20': '-20',
        '-30': '-30',
        '-40': '-40',
        '-50': '-50',
        '-60': '-60',
        '10': '10',
        '20': '20',
        '30': '30',
        '40': '40',
        '50': '50',
        '60': '60',
    },
  },
  },
  plugins: [
    require('flowbite/plugin')
  ],
  darkMode: 'class'
}
