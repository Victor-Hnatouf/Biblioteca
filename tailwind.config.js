import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';
import daisyui from 'daisyui';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'ink': '#120e0a',
                'ink-light': '#1c1815',
                'ink-dark': '#0a0806',
                'wood': '#8b5a2b',
                'wood-dark': '#5a3a1a',
                'gold': '#d4af37',
                'gold-light': '#f0d060',
                'gold-dim': '#8b6914',
                'parchment': '#e8dcca',
                'parchment-dim': '#c9bcae',
                'crimson': '#6b1010',
                'crimson-light': '#8b2020',
            }
        },
    },

    plugins: [forms, typography, daisyui],
    daisyui: {
        themes: [
            {
                medieval: {
                    "primary": "#8b5a2b",
                    "secondary": "#4a0e0e",
                    "accent": "#d4af37",
                    "neutral": "#1a1614",
                    "base-100": "#241f1c",
                    "base-200": "#1c1815",
                    "base-300": "#14110f",
                    "base-content": "#e8dcca",
                    "info": "#3abff8",
                    "success": "#36d399",
                    "warning": "#fbbd23",
                    "error": "#f87272",
                },
            },
        ],
    },
};
