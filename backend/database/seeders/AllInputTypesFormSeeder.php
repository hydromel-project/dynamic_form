<?php

namespace Database\Seeders;

use App\Models\Form;
use Illuminate\Database\Seeder;

class AllInputTypesFormSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define questions data first
        $questionsData = [
            [
                'question_text' => 'Enter your full name:',
                'type' => 'text',
                'required' => true,
                'order' => 1,
            ],
            [
                'question_text' => 'Enter your age:',
                'type' => 'number',
                'required' => true,
                'order' => 2,
            ],
            [
                'question_text' => 'Are you a robot?',
                'type' => 'boolean',
                'required' => true,
                'order' => 3,
            ],
            [
                'question_text' => 'Rate your satisfaction (1-10):',
                'type' => '1-10_scale',
                'required' => true,
                'order' => 4,
            ],
            [
                'question_text' => 'Upload your resume:',
                'type' => 'file',
                'required' => false,
                'order' => 5,
            ],
            [
                'question_text' => 'Take a selfie:',
                'type' => 'photo',
                'required' => false,
                'order' => 6,
            ],
            [
                'question_text' => 'Select your favorite color:',
                'type' => 'select',
                'options' => json_encode(['Red', 'Blue', 'Green', 'Yellow']),
                'required' => true,
                'order' => 7,
            ],
            [
                'question_text' => 'Do you agree to the terms and conditions?',
                'type' => 'boolean',
                'required' => true,
                'order' => 8,
            ],
            [
                'question_text' => 'Provide any additional comments:',
                'type' => 'text',
                'required' => false,
                'order' => 9,
            ],
        ];

        // Create the Form record with a temporary json_schema
        $form = Form::create([
            'name' => 'All Input Types Demo Form',
            'description' => 'A form demonstrating all available input types.',
            'json_schema' => [], // Temporary empty array
        ]);

        // Create the questions associated with the form
        $questions = $form->questions()->createMany($questionsData);

        // Now, update the form's json_schema with the actual question IDs
        $formSchema = $questions->map(function ($question) {
            return [
                'id' => $question->id,
                'question_text' => $question->question_text,
                'type' => $question->type,
                'options' => $question->options, // This will be null for non-select types
                'conditional_rules' => $question->conditional_rules, // This will be null for non-conditional types
                'required' => $question->required,
                'order' => $question->order,
            ];
        })->sortBy('order')->values()->all();

        $form->update([
            'json_schema' => $formSchema,
        ]);
    }
}