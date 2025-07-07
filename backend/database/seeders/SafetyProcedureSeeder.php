<?php

namespace Database\Seeders;

use App\Models\Form;
use App\Models\Question;
use App\Models\Response;
use App\Models\ResponseAnswer;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SafetyProcedureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a supervisor user
        $supervisor = User::factory()->create([
            'name' => 'Supervisor User',
            'email' => 'supervisor@example.com',
            'password' => bcrypt('password'),
        ]);
        $supervisor->assignRole('supervisor');

        // Create an employee user
        $employee = User::factory()->create([
            'name' => 'Employee User',
            'email' => 'employee@example.com',
            'password' => bcrypt('password'),
        ]);
        $employee->assignRole('respondent');

        // 1. Create the "Safety Procedure Check" form
        $form = Form::create([
            'name' => 'Safety Procedure Check',
            'description' => 'Daily safety checklist for factory employees and supervisors.',
            'json_schema' => [], // Will be populated with question IDs later
        ]);

        // 2. Define Questions Data
        $questionsData = [
            [
                'question_text' => 'Is the workplace clean and free of debris?',
                'type' => 'boolean',
                'required' => true,
                'order' => 1,
            ],
            [
                'question_text' => 'Are there objects blocking emergency exits or walkways?',
                'type' => 'boolean',
                'required' => true,
                'order' => 2,
            ],
            [
                'question_text' => 'If yes, please describe the obstruction.',
                'type' => 'text',
                'conditional_rules' => [
                    'show_if' => [
                        'question_id' => null, // Will be set dynamically
                        'operator' => 'equals',
                        'value' => true,
                    ],
                ],
                'required' => true,
                'order' => 3,
            ],
            [
                'question_text' => 'Are tools stored in their assigned places?',
                'type' => 'boolean',
                'required' => true,
                'order' => 4,
            ],
            [
                'question_text' => 'Which area was checked?',
                'type' => 'select',
                'options' => ['Assembly Line', 'Warehouse', 'Packing Area', 'Office'],
                'required' => true,
                'order' => 5,
            ],
            [
                'question_text' => 'Any other safety concerns?',
                'type' => 'text',
                'required' => false,
                'order' => 6,
            ],
        ];

        // Create the questions associated with the form and store them by their order for easy access
        $createdQuestions = [];
        foreach ($questionsData as $qData) {
            $question = $form->questions()->create($qData);
            $createdQuestions[$question->order] = $question;
        }

        // Update conditional rule for question 3 (If yes, please describe the obstruction.)
        // Now using the actual ID from the created question
        $createdQuestions[3]->update([
            'conditional_rules' => [
                'show_if' => [
                    'question_id' => $createdQuestions[2]->id, // Question 2: Are there objects blocking emergency exits or walkways?
                    'operator' => 'equals',
                    'value' => true,
                ],
            ],
        ]);

        // Build the json_schema for the form using the created questions' data
        $formSchema = collect($createdQuestions)->sortBy('order')->map(function ($question) {
            return [
                'id' => $question->id,
                'question_text' => $question->question_text,
                'type' => $question->type,
                'options' => $question->options, // This will be an array due to $casts in Model
                'conditional_rules' => $question->conditional_rules, // This will be an array due to $casts in Model
                'required' => (bool) $question->required,
                'order' => (int) $question->order,
            ];
        })->values()->all();

        // Update form's json_schema with question IDs and other data
        $form->update([
            'json_schema' => $formSchema,
        ]);

        // 3. Generate Responses and Answers
        // Response 1: All clear
        $response1 = Response::create([
            'form_id' => $form->id,
            'user_id' => $employee->id,
            'session_token' => Str::random(60),
            'submitted' => true,
        ]);
        ResponseAnswer::create(['response_id' => $response1->id, 'question_id' => $createdQuestions[1]->id, 'answer' => ['value' => false]]);
        ResponseAnswer::create(['response_id' => $response1->id, 'question_id' => $createdQuestions[2]->id, 'answer' => ['value' => false]]);
        ResponseAnswer::create(['response_id' => $response1->id, 'question_id' => $createdQuestions[4]->id, 'answer' => ['value' => true]]);
        ResponseAnswer::create(['response_id' => $response1->id, 'question_id' => $createdQuestions[5]->id, 'answer' => ['value' => 'Assembly Line']]);

        // Response 2: Obstruction found
        $response2 = Response::create([
            'form_id' => $form->id,
            'user_id' => $supervisor->id,
            'session_token' => Str::random(60),
            'submitted' => true,
        ]);
        ResponseAnswer::create(['response_id' => $response2->id, 'question_id' => $createdQuestions[1]->id, 'answer' => ['value' => true]]);
        ResponseAnswer::create(['response_id' => $response2->id, 'question_id' => $createdQuestions[2]->id, 'answer' => ['value' => true]]);
        ResponseAnswer::create(['response_id' => $response2->id, 'question_id' => $createdQuestions[3]->id, 'answer' => ['value' => 'Box blocking fire exit.']]);
        ResponseAnswer::create(['response_id' => $response2->id, 'question_id' => $createdQuestions[4]->id, 'answer' => ['value' => false]]);
        ResponseAnswer::create(['response_id' => $response2->id, 'question_id' => $createdQuestions[5]->id, 'answer' => ['value' => 'Warehouse']]);

        // Response 3: Incomplete (saved progress)
        $response3 = Response::create([
            'form_id' => $form->id,
            'user_id' => $employee->id,
            'session_token' => Str::random(60),
            'submitted' => false,
        ]);
        ResponseAnswer::create(['response_id' => $response3->id, 'question_id' => $createdQuestions[1]->id, 'answer' => ['value' => true]]);
        ResponseAnswer::create(['response_id' => $response3->id, 'question_id' => $createdQuestions[2]->id, 'answer' => ['value' => false]]);
    }
}