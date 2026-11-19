/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/views/public/kebijakan-privasi.blade.php",
    "./resources/views/public/verifikasi-dokumen.blade.php",
    "./resources/views/public/partials/**/*.blade.php",
  ],
  theme: {
    extend: {
      colors: {
        navy: {
          900: '#0c1b2e',
          800: '#1b3860',
          700: '#234a7d',
        },
        brand: {
          blue: '#0751FF',
          gold: '#f59e0b',
        }
      },
      fontFamily: {
        sans: ['"Open Sans"', 'Arial', 'sans-serif'],
        heading: ['"Plus Jakarta Sans"', 'sans-serif'],
        mono: ['"JetBrains Mono"', 'ui-monospace', 'monospace'],
      }
    },
  },
  plugins: [],
}
