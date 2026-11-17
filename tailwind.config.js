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
                maroon: {
                    50: '#FDF2F4',
                    100: '#FBE6EA',
                    200: '#F5C2CB',
                    500: '#A8234B',
                    600: '#9B1E43',
                    700: '#8C1D40',
                    800: '#731634',
                    900: '#5A1028',
                },
                gold: {
                    300: '#FFE27A',
                    400: '#FFD147',
                    500: '#FFC627',
                    600: '#E6AD13',
                    700: '#C2900B',
                },
                charcoal: {
                    700: '#3D3D3D',
                    800: '#2D2D2D',
                    900: '#222222',
                },
            },
        },
    },

    plugins: [forms],
};
