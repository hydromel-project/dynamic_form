import { useEffect } from 'react';
import { useAuth } from '@/context/AuthContext';

const SESSION_CHECK_INTERVAL = 5 * 60 * 1000; // Check every 5 minutes

const useSessionChecker = () => {
  const { isLoggedIn, logout } = useAuth();

  useEffect(() => {
    let intervalId: NodeJS.Timeout;

    const checkSession = async () => {
      if (isLoggedIn) {
        // In a real application, you'd hit a backend endpoint
        // that validates the token without returning sensitive data.
        // For now, we'll simulate a check or rely on subsequent API calls failing.
        // A simple way is to try fetching user data, and if it fails, log out.
        try {
          // Attempt to fetch user data - if this fails, the token is likely invalid
          // This implicitly uses the apiClient with its interceptor
          // If the backend /api/user endpoint returns 401, the interceptor will not handle it automatically
          // so we need to handle it here.
          const response = await fetch('/api/user', {
            headers: {
              'Authorization': `Bearer ${localStorage.getItem('authToken')}`,
            },
          });

          if (response.status === 401) {
            console.log('Session invalid, logging out.');
            await logout();
          }
        } catch (error) {
          console.error('Error during session check:', error);
          // If there's a network error or other issue, assume session might be invalid
          await logout();
        }
      }
    };

    // Run immediately on mount if logged in
    if (isLoggedIn) {
      checkSession();
      intervalId = setInterval(checkSession, SESSION_CHECK_INTERVAL);
    }

    return () => {
      if (intervalId) {
        clearInterval(intervalId);
      }
    };
  }, [isLoggedIn, logout]);
};

export default useSessionChecker;
