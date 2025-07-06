import http from '@/api/http';
import { Form } from '@/types/form';

export const getForms = async (): Promise<Form[]> => {
  try {
    const response = await http.get<Form[]>('/forms');
    return response.data;
  } catch (error) {
    console.error('Error fetching forms:', error);
    throw error;
  }
};

export const getFormById = async (id: number): Promise<Form> => {
  try {
    const response = await http.get<Form>(`/forms/${id}`);
    return response.data;
  } catch (error) {
    console.error(`Error fetching form with ID ${id}:`, error);
    throw error;
  }
};

export const submitResponse = async (formId: number, answers: any): Promise<any> => {
  try {
    // This is a simplified example. The actual backend might require a session token first.
    // For now, we'll assume a direct submission or that the session is handled by the interceptor.
    const response = await http.post(`/forms/${formId}/responses`, { answers });
    return response.data;
  } catch (error) {
    console.error(`Error submitting response for form ${formId}:`, error);
    throw error;
  }
};
