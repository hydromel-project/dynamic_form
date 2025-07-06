export interface Form {
  id: number;
  name: string;
  description: string;
  json_schema: FormSchema;
  created_at: string;
  updated_at: string;
}

export interface FormSchema {
  questions: Question[];
}

export interface Question {
  id: number;
  form_id: number;
  question_text: string;
  type: 'text' | 'number' | 'boolean' | '1-10_scale' | 'file' | 'photo';
  options?: any; // JSON for options, e.g., for multiple choice or scale labels
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