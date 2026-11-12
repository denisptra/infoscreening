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
            colors: {
                brand: {
                    DEFAULT: '#3A07BB',
                    50: '#f6f3fe',
                    100: '#ece5fd',
                    200: '#ded0fc',
                    300: '#c5aefa',
                    400: '#a680f5',
                    500: '#8751ee',
                    600: '#7133e3',
                    700: '#5a1fcc',
                    800: '#4615a6',
                    900: '#3A07BB',
                    950: '#230373',
                },
                strapi: {
                    bg: '#f6f6f9',
                    sidebar: '#212134',
                    'sidebar-hover': '#2d2d44',
                    'sidebar-active': '#32324d',
                    border: '#eaeaef',
                    text: '#32324d',
                    muted: '#666687',
                    neutral100: '#f6f6f9',
                    neutral150: '#eaeaef',
                    neutral200: '#dcdce4',
                    neutral600: '#666687',
                    neutral800: '#32324d',
                    primary: '#3A07BB',
                    'primary-light': '#f0ebfc',
                },
                ink: '#1e293b',
                paper: '#ffffff',
                'ink-muted': '#64748b',
                border: '#e2e8f0',
            },
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                body: ['Inter', ...defaultTheme.fontFamily.sans],
                heading: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            spacing: {
                '0.2': '0.05rem',
                '4.5': '1.125rem',
            },
            borderWidth: {
                '3': '3px',
            },
            backdropBlur: {
                'xs': '2px',
            },
            boxShadow: {
                'xs': '0 1px 2px 0 rgba(0, 0, 0, 0.05)',
                'card': '0 1px 3px 0 rgba(0, 0, 0, 0.04), 0 1px 2px -1px rgba(0, 0, 0, 0.04)',
                'card-hover': '0 10px 25px -5px rgba(58, 7, 187, 0.08), 0 8px 10px -6px rgba(58, 7, 187, 0.04)',
                'strapi': '0 1px 4px rgba(33, 33, 52, 0.06), 0 1px 2px rgba(33, 33, 52, 0.04)',
                'strapi-card': '0 2px 8px rgba(33, 33, 52, 0.05)',
            },
            borderRadius: {
                'xl': '0.75rem',
                '2xl': '1rem',
                '3xl': '1.5rem',
            },
        },
    },

    plugins: [forms],
};
