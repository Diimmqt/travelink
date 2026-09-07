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
                // ── Brex Design System Tokens ──
                brex: {
                    paper:    '#ffffff', // Canvas & card background
                    fog:      '#f3f3f7', // Alternate section background
                    mist:     '#b9bbc6', // Hairline border 1px
                    ink:      '#000000', // Main heading & primary text
                    graphite: '#60646c', // Body paragraph text
                    pewter:   '#6f737b', // Caption, helper & small labels
                    steel:    '#8b8d98', // Placeholder & secondary icons
                    ember:    '#ff5900', // STRICT SINGLE ACCENT: Primary CTA fill & standalone links
                    abyss:    '#000710', // Dark frame background for footer
                    carbon:   '#15191e', // Dark frame background for announcement bar
                },

                // ── Legacy brand tokens ──
                brand: {
                    navy: {
                        50: '#f8fafc',
                        100: '#f1f5f9',
                        200: '#e2e8f0',
                        300: '#cbd5e1',
                        400: '#94a3b8',
                        500: '#64748b',
                        600: '#475569',
                        700: '#334155',
                        800: '#1e293b',
                        900: '#0f172a',
                        950: '#020617',
                    },
                    amber: {
                        50: '#f5f7ff',
                        100: '#eef2ff',
                        200: '#e0e7ff',
                        300: '#c7d2fe',
                        400: '#a5b4fc',
                        500: '#6366f1',
                        600: '#4f46e5',
                        700: '#4338ca',
                        800: '#3730a3',
                        900: '#312e81',
                        950: '#1e1b4b',
                    },
                    stone: {
                        50: '#fafaf9',
                        100: '#f5f5f4',
                        200: '#e7e5e4',
                        300: '#d6d3d1',
                        400: '#a8a29e',
                        500: '#78716c',
                        600: '#57534e',
                        700: '#44403c',
                        800: '#292524',
                        900: '#1c1917',
                        950: '#0c0a09',
                    }
                },
            },

            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                heading: ['Inter', ...defaultTheme.fontFamily.sans],
            },

            letterSpacing: {
                'brex-72': '-0.03em',
                'brex-48': '-0.025em',
                'brex-36': '-0.02em',
                'brex-24': '-0.01em',
            },

            borderRadius: {
                'brex': '12px',
                'brex-chip': '6px',
            },

            boxShadow: {
                'brex-modal': '0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.05)',
                'none': 'none',
            },
        },
    },

    plugins: [forms],
};
