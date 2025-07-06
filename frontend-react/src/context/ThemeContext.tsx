import React, { createContext, useContext, useState, useEffect, ReactNode } from 'react';

type Theme = 'light' | 'dark' | 'tokyo-light' | 'tokyo-dark';

interface ThemeContextType {
  theme: Theme;
  setTheme: (theme: Theme) => void;
  toggleDarkMode: () => void;
  toggleTokyoNight: () => void;
}

const ThemeContext = createContext<ThemeContextType | undefined>(undefined);

export const ThemeProvider: React.FC<{ children: ReactNode }> = ({ children }) => {
  const [theme, setThemeState] = useState<Theme>(() => {
    // Initialize theme from localStorage or system preference
    const savedTheme = localStorage.getItem('theme') as Theme;
    if (savedTheme) {
      return savedTheme;
    }
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
  });

  useEffect(() => {
    const root = window.document.documentElement;
    // Remove all theme-related classes first
    root.classList.remove('light', 'dark', 'theme-tokyo-light', 'theme-tokyo-dark');

    // Add the current theme class
    if (theme === 'dark') {
      root.classList.add('dark');
    } else if (theme === 'tokyo-light') {
      root.classList.add('theme-tokyo-light');
    } else if (theme === 'tokyo-dark') {
      root.classList.add('theme-tokyo-dark');
    } else {
      root.classList.add('light'); // Default to light if no specific dark/tokyo theme
    }

    localStorage.setItem('theme', theme);
  }, [theme]);

  const setTheme = (newTheme: Theme) => {
    setThemeState(newTheme);
  };

  const toggleDarkMode = () => {
    setThemeState((prevTheme) => (prevTheme === 'dark' ? 'light' : 'dark'));
  };

  const toggleTokyoNight = () => {
    setThemeState((prevTheme) => {
      if (prevTheme === 'tokyo-dark') return 'light';
      if (prevTheme === 'tokyo-light') return 'tokyo-dark';
      return 'tokyo-light';
    });
  };

  return (
    <ThemeContext.Provider value={{ theme, setTheme, toggleDarkMode, toggleTokyoNight }}>
      {children}
    </ThemeContext.Provider>
  );
};

export const useTheme = () => {
  const context = useContext(ThemeContext);
  if (context === undefined) {
    throw new Error('useTheme must be used within a ThemeProvider');
  }
  return context;
};
