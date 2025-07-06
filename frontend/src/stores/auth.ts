import { defineStore } from 'pinia';
import axios from 'axios';

interface AuthState {
  token: string | null;
  user: any | null; // You might want to define a more specific User type
}

export const useAuthStore = defineStore('auth', {
  state: (): AuthState => ({
    token: localStorage.getItem('auth_token'),
    user: null,
  }),
  getters: {
    isAuthenticated: (state) => !!state.token,
  },
  actions: {
    async login(email: string, password: string) {
      try {
        const response = await axios.post('http://127.0.0.1:8000/api/login', {
          email,
          password,
        });
        this.token = response.data.access_token;
        localStorage.setItem('auth_token', this.token as string);
        // Optionally fetch user data
        // const userResponse = await axios.get('http://127.0.0.1:8000/api/user');
        // this.user = userResponse.data;
        return true;
      } catch (error) {
        this.token = null;
        localStorage.removeItem('auth_token');
        this.user = null;
        throw error;
      }
    },
    logout() {
      this.token = null;
      localStorage.removeItem('auth_token');
      this.user = null;
      // Optionally call backend logout endpoint
      // axios.post('http://127.0.0.1:8000/api/logout');
    },
  },
});
