import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
  content: ['./src/**/*.{astro,html,js,jsx,md,mdx,svelte,ts,tsx,vue}'],
  theme: {
    extend: {
      colors: {
        teyvat: {
          night: '#1a1a2e',
          deep: '#16213e',
          accent: '#e8b445',
          gold: '#f5d68a',
          mint: '#4ecdc4',
          muted: '#94a3b8',
        },
        element: {
          pyro: '#ff6b4a',
          hydro: '#4fc3f7',
          electro: '#b388ff',
          cryo: '#81d4fa',
          anemo: '#69f0ae',
          geo: '#ffd54f',
          dendro: '#a5d6a7',
        },
      },
      fontFamily: {
        display: ['Cinzel', 'Georgia', 'serif'],
        sans: [
          'system-ui',
          '-apple-system',
          'Segoe UI',
          'Roboto',
          'Ubuntu',
          'sans-serif',
        ],
      },
      boxShadow: {
        glow: '0 0 24px rgba(232, 180, 69, 0.25)',
        card: '0 8px 32px rgba(0, 0, 0, 0.35)',
      },
      backgroundImage: {
        'teyvat-gradient':
          'radial-gradient(ellipse 120% 80% at 50% -20%, rgba(232,180,69,0.12), transparent 50%), linear-gradient(180deg, #1a1a2e 0%, #0f0f1a 100%)',
      },
    },
  },
  plugins: [typography],
};
