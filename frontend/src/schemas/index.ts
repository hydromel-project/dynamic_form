import { z } from 'zod';

export const FormSchema = z.object({
  id: z.number(),
  name: z.string(),
  description: z.string().nullable(),
  json_schema: z.array(z.any()), // This will be refined as we define question schemas
  created_at: z.string().datetime().nullable(),
  updated_at: z.string().datetime().nullable(),
});

export const QuestionSchema = z.object({
  id: z.number(),
  form_id: z.number(),
  question_text: z.string(),
  type: z.string(), // e.g., 'text', 'number', 'boolean', 'file'
  options: z.array(z.any()).nullable(), // This will be refined based on question type
  conditional_rules: z.object({
    show_if: z.object({
      question_id: z.number(),
      operator: z.string(), // e.g., 'equals', 'not_equals'
      value: z.any(),
    }).optional(),
  }).nullable(),
  required: z.boolean(),
  order: z.number(),
  created_at: z.string().datetime().nullable(),
  updated_at: z.string().datetime().nullable(),
});

export const ResponseSchema = z.object({
  id: z.number(),
  form_id: z.number(),
  user_id: z.number().nullable(),
  session_token: z.string(),
  submitted: z.boolean(),
  created_at: z.string().datetime().nullable(),
  updated_at: z.string().datetime().nullable(),
});

export const ResponseAnswerSchema = z.object({
  id: z.number(),
  response_id: z.number(),
  question_id: z.number(),
  answer: z.any(), // Can be string, number, boolean, or object for file uploads
  created_at: z.string().datetime().nullable(),
  updated_at: z.string().datetime().nullable(),
});

export type Form = z.infer<typeof FormSchema>;
export type Question = z.infer<typeof QuestionSchema>;
export type Response = z.infer<typeof ResponseSchema>;
export type ResponseAnswer = z.infer<typeof ResponseAnswerSchema>;
