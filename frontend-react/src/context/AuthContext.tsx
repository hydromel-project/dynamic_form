import React, { createContext, useContext, useState, useEffect, ReactNode } from 'react';
import { login as apiLogin, logout as apiLogout, isAuthenticated as checkAuth, getToken } from '@/services/session';

interface AuthContextType {
  isLoggedIn: boolean;
  user: { id: number; name: string; email: string } | null;
  login: (email: string, password: string) => Promise<void>;
  logout: () => Promise<void>;
}

const AuthContext = createContext<AuthContextType | undefined>(undefined);

export const AuthProvider: React.FC<{ children: ReactNode }> = ({ children }) => {
  const [isLoggedIn, setIsLoggedIn] = useState(false);
  const [user, setUser] = useState<{ id: number; name: string; email: string } | null>(null);

  useEffect(() => {
    const token = getToken();
    if (token) {
      // In a real app, you'd validate the token with the backend
      // and fetch user details. For now, we'll just assume it's valid.
      setIsLoggedIn(true);
      // Placeholder user data - replace with actual user data from token/API
      setUser({ id: 1, name: 'Authenticated User', email: 'user@example.com' });
    } else {
      setIsLoggedIn(false);
      setUser(null);
    }
  }, []);

  const login = async (email: string, password: string) => {
    try {
      const authResponse = await apiLogin(email, password);
      setIsLoggedIn(true);
      setUser(authResponse.user);
    } catch (error) {
      setIsLoggedIn(false);
      setUser(null);
      throw error;
    }
  };

  const logout = async () => {
    try {
      await apiLogout();
      setIsLoggedIn(false);
      setUser(null);
    } catch (error) {
      console.error("Logout failed on API side, but clearing session locally.", error);
      setIsLoggedIn(false);
      setUser(null);
    }
  };

  return (
    <AuthContext.Provider value={{ isLoggedIn, user, login, logout }}>
      {children}
    </AuthContext.Provider>
  );
};

export const useAuth = () => {
  const context = useContext(AuthContext);
  if (context === undefined) {
    throw new Error('useAuth must be used within an AuthProvider');
  }
  return context;
};
