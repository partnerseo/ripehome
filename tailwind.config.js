/** @type {import('tailwindcss').Config} */
export default {
  content: ['./index.html', './src/**/*.{js,ts,jsx,tsx}'],
  theme: {
    extend: {
      keyframes: {
        wiggle: {
          '0%, 100%': { transform: 'rotate(-3deg)' },
          '50%': { transform: 'rotate(3deg)' },
        },
        'pulse-slow': {
          '0%, 100%': { opacity: '1' },
          '50%': { opacity: '0.82' },
        },
      },
      animation: {
        wiggle: 'wiggle 1s ease-in-out infinite',
        'pulse-slow': 'pulse-slow 2s ease-in-out infinite',
      },
    },
  },
  plugins: [],
};
