const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
  important: true,

  theme: {
    screens: {
      sm: '640px',
      md: '768px',
      lg: '1024px',
      xl: '1280px',
    },
    colors: {
      transparent: 'transparent',
      primary: '#5c6ac4',
      secondary: '#007ace',
      red: '#de3618',
      white: '#ffffff',
      black: '#000000',
      'gray-light': '#e6e6e6',
      'gray-medium': '#cacaca',
      'gray-dark': '#8a8a8a',
    },
    fontFamily: {
      sans: [...defaultTheme.fontFamily.sans],
      serif: [...defaultTheme.fontFamily.serif],
      mono: [...defaultTheme.fontFamily.mono],
    },
    fontSize: {
      'xs': '12px',
      'sm': '14px',
      'base': '16px',
      'lg': '18px',
      'xl': '20px',
      '2xl': '24px',
      '3xl': '30px',
      '4xl': '36px',
      '5xl': '48px',
      '6xl': '64px',
    },
    spacing: {
      '0': '0',
      '1': '8px',
      '2': '12px',
      '3': '16px',
      '4': '24px',
      '5': '32px',
      '6': '48px',
    },
  },
  variants: {},
  plugins: [],
}
