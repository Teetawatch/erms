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
                    bg: '#f6f8f9',
                    surface: '#ffffff',
                    'surface-2': '#f1f3f5',
                    'surface-3': '#e8ecee',
                    border: '#e0e4e8',
                    'border-light': '#eceef0',
                    blue: '#4573d2',
                    'blue-light': '#e8edfb',
                    purple: '#7c5cfc',
                    'purple-light': '#f0ecff',
                    green: '#5da283',
                    'green-light': '#e6f5ef',
                    orange: '#ec8d49',
                    'orange-light': '#fef3e7',
                    red: '#d1395c',
                    'red-light': '#fce4ec',
                    yellow: '#e8a442',
                    'yellow-light': '#fff8e7',
                    pink: '#e362a4',
                    teal: '#37b4b1',
                    text: '#1e1f21',
                    'text-secondary': '#6d6e6f',
                    muted: '#9ca0a4',
                    'sidebar-bg': '#2e2e38',
                    'sidebar-hover': '#393942',
                    'sidebar-active': '#454550',
                    'sidebar-text': '#a2a0a8',
                    'sidebar-text-active': '#ffffff',
                },
            },
            fontFamily: {
                heading: ['Inter', 'Noto Sans Thai', ...defaultTheme.fontFamily.sans],
                sans: ['Inter', 'Noto Sans Thai', ...defaultTheme.fontFamily.sans],
            },
            fontSize: {
                '2xs': ['0.6875rem', { lineHeight: '1rem' }],
            },
            boxShadow: {
                'asana': '0 1px 3px 0 rgba(21, 27, 38, 0.08)',
                'asana-md': '0 4px 12px 0 rgba(21, 27, 38, 0.08)',
                'asana-lg': '0 8px 24px 0 rgba(21, 27, 38, 0.12)',
                'asana-card': '0 1px 4px rgba(0,0,0,0.08)',
                'asana-card-hover': '0 2px 8px rgba(0,0,0,0.12)',
                'panel': '−4px 0 16px rgba(0,0,0,0.08)',
            },
            animation: {
                'slide-in-right': 'slideInRight 0.25s cubic-bezier(0.4, 0, 0.2, 1)',
                'slide-out-right': 'slideOutRight 0.2s cubic-bezier(0.4, 0, 0.2, 1)',
                'fade-in': 'fadeIn 0.2s ease-out',
                'fade-in-up': 'fadeInUp 0.25s ease-out',
                'scale-in': 'scaleIn 0.15s ease-out',
                'shimmer': 'shimmer 1.5s infinite',
            },
            keyframes: {
                slideInRight: {
                    '0%': { transform: 'translateX(100%)', opacity: '0' },
                    '100%': { transform: 'translateX(0)', opacity: '1' },
                },
                slideOutRight: {
                    '0%': { transform: 'translateX(0)', opacity: '1' },
                    '100%': { transform: 'translateX(100%)', opacity: '0' },
                },
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                fadeInUp: {
                    '0%': { opacity: '0', transform: 'translateY(8px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                scaleIn: {
                    '0%': { opacity: '0', transform: 'scale(0.95)' },
                    '100%': { opacity: '1', transform: 'scale(1)' },
                },
                shimmer: {
                    '0%': { backgroundPosition: '-200% 0' },
                    '100%': { backgroundPosition: '200% 0' },
                },
            },
            transitionTimingFunction: {
                'asana': 'cubic-bezier(0.4, 0, 0.2, 1)',
            },
        },
    },

    plugins: [forms],
};
