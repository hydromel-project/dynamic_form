export interface Form {
  id: number;
  name: string;
  description: string;
  json_schema: Question[]; // Changed from FormSchema to Question[]
  created_at: string;
  updated_at: string;
}

// FormSchema interface is no longer needed if json_schema is directly Question[]
// export interface FormSchema {
//   questions: Question[];
// }

export interface Question {
  id: number;
  form_id: number;
  question_text: string;
  type: 'text' | 'number' | 'boolean' | '1-10_scale' | 'file' | 'photo' | 'select'; // Added 'select'
  options?: string[]; // Assuming options for select are string arrays
  conditional_rules?: ConditionalRule; // JSON for conditional logic
  required: boolean;
  order: number;
  created_at: string;
  updated_at: string;
}

export interface ConditionalRule {
  show_if: {
    question_id: number;
    operator: 'equals' | 'not_equals' | 'greater_than' | 'less_than';
    value: any;
  };
}

export interface Response {
  id: number;
  form_id: number;
  user_id: number | null;
  session_token: string;
  submitted: boolean;
  created_at: string;
  updated_at: string;
}

export interface ResponseAnswer {
  id: number;
  response_id: number;
  question_id: number;
  answer: any; // Flexible for all types, e.g., string, number, boolean, file path
  created_at: string;
  updated_at: string;
}
