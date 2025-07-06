import apiClient from '@/api/apiClient';

interface AuthResponse {
  token: string;
  user: {
    id: number;
    name: string;
    email: string;
  };
}

export const login = async (email: string, password: string): Promise<AuthResponse> => {
  try {
    const response = await apiClient.post<AuthResponse>('/login', { email, password });
    localStorage.setItem('authToken', response.data.token);
    return response.data;
  } catch (error) {
    console.error('Login failed:', error);
    throw error;
  }
};

export const logout = async (): Promise<void> => {
  try {
    await apiClient.post('/logout');
    localStorage.removeItem('authToken');
  } catch (error) {
    console.error('Logout failed:', error);
    throw error;
  }
};

export const isAuthenticated = (): boolean => {
  return !!localStorage.getItem('authToken');
};

export const getToken = (): string | null => {
  return localStorage.getItem('authToken');
};
