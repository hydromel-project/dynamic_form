import apiClient from './apiClient';

const http = {
  get: <T>(url: string, config?: object) => apiClient.get<T>(url, config),
  post: <T>(url: string, data?: any, config?: object) => apiClient.post<T>(url, data, config),
  put: <T>(url: string, data?: any, config?: object) => apiClient.put<T>(url, data, config),
  patch: <T>(url: string, data?: any, config?: object) => apiClient.patch<T>(url, data, config),
  del: <T>(url: string, config?: object) => apiClient.delete<T>(url, config),
};

export default http;
