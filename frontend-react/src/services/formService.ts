import http from '@/api/http';
import { Form } from '@/types/form';

interface StartResponseResponse {
  session_token: string;
  // Add other properties if the backend returns them, e.g., response_id
}

export const getForms = async (): Promise<Form[]> => {
  try {
    const response = await http.get<{ data: Form[] }>('/forms'); // Expect a 'data' wrapper
    return response.data.data; // Access the 'data' property
  } catch (error) {
    console.error('Error fetching forms:', error);
    throw error;
  }
};

export const getFormById = async (id: number): Promise<Form> => {
  try {
    const response = await http.get<{ data: Form }>(`/forms/${id}`); // Expect a 'data' wrapper for single resource
    return response.data.data; // Access the 'data' property
  } catch (error) {
    console.error(`Error fetching form with ID ${id}:`, error);
    throw error;
  }
};

export const submitResponse = async (formId: number, formData: FormData): Promise<any> => {
  try {
    // 1. Start a response session to get a session_token
    const startResponse = await http.post<StartResponseResponse>('/responses/start', { form_id: formId });
    const sessionToken = startResponse.data.session_token;

    // 2. Save the answers using the session_token
    // Pass FormData directly. Axios will set Content-Type to multipart/form-data automatically.
    await http.post(`/responses/${sessionToken}/save`, formData);

    // 3. Submit the completed form using the session_token
    const response = await http.post(`/responses/${sessionToken}/submit`); // No answers payload needed for submit
    return response.data;
  } catch (error) {
    console.error(`Error submitting response for form ${formId}:`, error);
    throw error;
  }
};