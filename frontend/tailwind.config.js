/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        // Tokyo Night inspired palette
        'tn-primary': '#7aa2f7', // Blue
        'tn-secondary': '#bb9af7', // Purple
        'tn-success': '#9ece6a', // Green
        'tn-info': '#7dcfff', // Light Blue
        'tn-warning': '#e0af68', // Orange
        'tn-danger': '#f7768e', // Red
        'tn-background': '#1a1b26', // Dark background
        'tn-background-light': '#24283b', // Slightly lighter background
        'tn-background-lighter': '#414868', // Even lighter background / borders
        'tn-on-surface': '#c0caf5', // Text color
        'tn-surface': '#1a1b26', // Card/surface background
      },
    },
  },
  plugins: [],
}
