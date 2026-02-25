/** @type {import('tailwindcss').Config} */
export default {
    content: ['./templates/**/*.twig'],
    plugins: [require('daisyui')],
    daisyui: {
        themes: ['light', 'dark'],
    },
};
