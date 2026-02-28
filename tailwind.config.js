import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './node_modules/flowbite/**/*.js',
    ],

    theme: {
        extend: {
            colors: {
                erms: {
                    bg: '#f4f6fb',
                    surface: '#ffffff',
                    'surface-2': '#f0f2f7',
                    border: '#e2e6ef',
                    blue: '#4f8ef7',
                    purple: '#7c5cfc',
                    green: '#16b97a',
                    orange: '#f17b2c',
                    red: '#ef4444',
                    yellow: '#e5a00d',
                    text: '#1e293b',
                    muted: '#64748b',
                },
            },
            fontFamily: {
                heading: ['Kanit', ...defaultTheme.fontFamily.sans],
                sans: ['Kanit', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
