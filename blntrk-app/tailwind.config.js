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
                sans: ['"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
                display: ['"Bricolage Grotesque"', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                forest: {
                    50: '#f3f7f4',
                    100: '#e4ece6',
                    200: '#c5d7cb',
                    300: '#9bbba4',
                    400: '#6f9c7a',
                    500: '#4e7a5a',
                    600: '#3a6245',
                    700: '#2d5440',
                    800: '#1e3a2c',
                    850: '#14201a',
                    900: '#0e1b15',
                    950: '#0a140f',
                },
                lantern: {
                    50: '#fdfbf0',
                    100: '#fef6e0',
                    200: '#fce8b8',
                    300: '#fad685',
                    400: '#ffd36e',
                    500: '#f6c453',
                    600: '#e0a526',
                    700: '#b87c18',
                    800: '#956019',
                    900: '#7a4f1a',
                },
            },
        },
    },

    plugins: [forms],
};
