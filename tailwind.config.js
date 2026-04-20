import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'vc-berry': '#BF1F6A',
                'vc-magenta': '#F2059F',
                'vc-purple': '#8704BF',
                'vc-indigo': '#5F1BF2',
                'vc-cyan': '#00F0FF', // Added for coherence with new logo
            },
        },
    },

    plugins: [forms],
};
