import React, { ReactNode, useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { Button } from '@/components/ui/button';
import { Sheet, SheetContent, SheetTrigger } from '@/components/ui/sheet';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import { Separator } from '@/components/ui/separator';
import { Home, FormInput, LayoutDashboard, Menu, Sun, Moon, Palette } from 'lucide-react';
import { useTheme } from '@/components/theme-provider';
import { useAuth } from '@/context/AuthContext'; // Import useAuth

interface LayoutProps {
  children: ReactNode;
}

const navigationItems = [
  { href: '/', label: 'Home', icon: Home },
  { href: '/forms', label: 'Forms', icon: FormInput },
  { href: '/dashboard', label: 'Dashboard', icon: LayoutDashboard },
];

const Layout: React.FC<LayoutProps> = ({ children }) => {
  const navigate = useNavigate();
  const { theme, setTheme } = useTheme();
  const { isLoggedIn, logout } = useAuth(); // Use isLoggedIn and logout from useAuth
  const [isSidebarCollapsed, setIsSidebarCollapsed] = useState(false);

  const handleLogout = async () => {
    await logout(); // Use logout from AuthContext
    navigate('/login');
  };

  const toggleDarkMode = () => {
    setTheme(theme === 'dark' ? 'light' : 'dark');
  };

  const toggleTokyoNight = () => {
    if (theme === 'tokyo-dark') {
      setTheme('light');
    } else if (theme === 'tokyo-light') {
      setTheme('tokyo-dark');
    } else {
      setTheme('tokyo-light');
    }
  };

  return (
    <div className="min-h-screen flex bg-background text-foreground">
      {/* Desktop Sidebar */}
      <aside
        className={`hidden md:flex flex-col h-screen border-r bg-card text-card-foreground transition-all duration-300 ease-in-out
          ${isSidebarCollapsed ? 'w-20' : 'w-64'}`}
      >
        <div className="flex items-center justify-center h-16 border-b px-4">
          {!isSidebarCollapsed ? (
            <Link to="/" className="text-xl font-bold whitespace-nowrap">
              Dynamic Forms
            </Link>
          ) : (
            <Link to="/" className="text-xl font-bold">
              DF
            </Link>
          )}
        </div>
        <nav className="flex-1 px-2 py-4 space-y-1">
          {isLoggedIn && navigationItems.map((item) => ( // Conditionally render navigation items
            <TooltipProvider key={item.href}>
              <Tooltip>
                <TooltipTrigger asChild>
                  <Link
                    to={item.href}
                    className={`flex items-center rounded-md px-3 py-2 text-sm font-medium hover:bg-accent hover:text-accent-foreground
                      ${isSidebarCollapsed ? 'justify-center' : ''}`}
                  >
                    <item.icon className={`h-5 w-5 ${!isSidebarCollapsed ? 'mr-3' : ''}`} />
                    {!isSidebarCollapsed && item.label}
                  </Link>
                </TooltipTrigger>
                {isSidebarCollapsed && <TooltipContent side="right">{item.label}</TooltipContent>}
              </Tooltip>
            </TooltipProvider>
          ))}
        </nav>
        <div className="p-4 border-t">
          <Button
            variant="outline"
            className="w-full"
            onClick={() => setIsSidebarCollapsed(!isSidebarCollapsed)}
          >
            {isSidebarCollapsed ? 'Expand' : 'Collapse'}
          </Button>
        </div>
      </aside>

      {/* Main Content Area */}
      <div className="flex-1 flex flex-col">
        {/* Mobile Header */}
        <header className="md:hidden flex items-center justify-between h-16 border-b bg-card text-card-foreground px-4">
          <Sheet>
            <SheetTrigger asChild>
              <Button variant="outline" size="icon">
                <Menu className="h-6 w-6" />
              </Button>
            </SheetTrigger>
            <SheetContent side="left" className="w-64">
              <div className="flex items-center h-16 border-b px-4">
                <Link to="/" className="text-xl font-bold">
                  Dynamic Forms
                </Link>
              </div>
              <nav className="flex-1 px-2 py-4 space-y-1">
                {isLoggedIn && navigationItems.map((item) => ( // Conditionally render navigation items
                  <Link
                    key={item.href}
                    to={item.href}
                    className="flex items-center rounded-md px-3 py-2 text-sm font-medium hover:bg-accent hover:text-accent-foreground"
                  >
                    <item.icon className="h-5 w-5 mr-3" />
                    {item.label}
                  </Link>
                ))}
              </nav>
              <div className="p-4 border-t">
                {isLoggedIn ? (
                  <Button variant="destructive" onClick={handleLogout} className="w-full">
                    Logout
                  </Button>
                ) : (
                  <Link to="/login" className="w-full">
                    <Button className="w-full">Login</Button>
                  </Link>
                )}
              </div>
            </SheetContent>
          </Sheet>
          <Link to="/" className="text-xl font-bold">
            Dynamic Forms
          </Link>
          {/* Theme Toggles for Mobile */}
          <div className="flex items-center space-x-2">
            <Button variant="outline" size="icon" onClick={toggleDarkMode}>
              {theme === 'dark' ? <Sun className="h-5 w-5" /> : <Moon className="h-5 w-5" />}
            </Button>
            <Button variant="outline" size="icon" onClick={toggleTokyoNight}>
              <Palette className="h-5 w-5" />
            </Button>
          </div>
        </header>

        <main className="flex-1 overflow-auto p-4">
          {children}
        </main>

        <footer className="border-t bg-card text-card-foreground p-4 text-center text-sm">
          <p>&copy; 2024 Dynamic Forms. All rights reserved.</p>
        </footer>
      </div>
    </div>
  );
};

export default Layout;