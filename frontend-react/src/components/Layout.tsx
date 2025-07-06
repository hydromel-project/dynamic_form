import React, { ReactNode } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { isAuthenticated, logout } from '@/services/session';
import { useTheme } from '@/context/ThemeContext';
import { Button } from '@/components/ui/button';

interface LayoutProps {
  children: ReactNode;
}

const Layout: React.FC<LayoutProps> = ({ children }) => {
  const navigate = useNavigate();
  const { theme, toggleDarkMode, toggleTokyoNight } = useTheme();
  const isLoggedIn = isAuthenticated();

  const handleLogout = () => {
    logout();
    navigate('/login');
  };

  return (
    <div className="min-h-screen flex flex-col bg-background text-text transition-colors duration-300">
      <header className="bg-primary text-primary-foreground p-4 shadow-md">
        <nav className="container mx-auto flex justify-between items-center">
          <Link to="/" className="text-2xl font-bold">
            Dynamic Forms
          </Link>
          <div className="flex items-center space-x-4">
            {isLoggedIn && (
              <Link to="/forms" className="hover:underline">
                Forms
              </Link>
            )}
            {isLoggedIn && (
              <Link to="/dashboard" className="hover:underline">
                Dashboard
              </Link>
            )}
            <Button
              variant="outline"
              size="sm"
              onClick={toggleDarkMode}
            >
              {theme === 'dark' ? 'Light Mode' : 'Dark Mode'}
            </Button>
            <Button
              variant="outline"
              size="sm"
              onClick={toggleTokyoNight}
            >
              {theme.includes('tokyo') ? 'Default Theme' : 'Tokyo Night'}
            </Button>
            {isLoggedIn ? (
              <Button
                variant="destructive"
                onClick={handleLogout}
              >
                Logout
              </Button>
            ) : (
              <Link to="/login">
                <Button variant="default">
                  Login
                </Button>
              </Link>
            )}
          </div>
        </nav>
      </header>
      <main className="flex-grow container mx-auto p-4">
        {children}
      </main>
      <footer className="bg-gray-800 text-white p-4 text-center">
        <p>&copy; 2024 Dynamic Forms. All rights reserved.</p>
      </footer>
    </div>
  );
};

export default Layout;