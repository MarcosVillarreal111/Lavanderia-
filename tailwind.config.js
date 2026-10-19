import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './app/Livewire/**/*.php',
    ],

    theme: {
        extend: {
            colors: {
                espuma: {
                    yellow: '#F5D25F',
                    'yellow-dark': '#E6BE3A',
                    navy: '#1B2540',
                    cream: '#FBF8F1',
                    muted: '#6B7280',
                    green: '#25D366',
                },
            },
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms, typography],
};
