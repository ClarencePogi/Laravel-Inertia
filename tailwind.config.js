import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            keyframes: {
                flip: {
                  '0%': { transform: 'rotateY(0deg)' },
                  '100%': { transform: 'rotateY(180deg)' },
                },
              },
              animation: {
                flip: 'flip 0.6s ease-out forwards',
                'flip-back': 'flip-back 0.6s ease-out forwards',
              },
        },
    },

    plugins: [forms],
};
