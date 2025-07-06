<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use App\Models\Form;
use App\Models\Question;
use App\Models\Response;
use App\Models\ResponseAnswer;
use Laravel\Sanctum\Sanctum;

class ResponseApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that a response session can be started.
     */
    public function test_response_session_can_be_started(): void
    {
        $user = \App\Models\User::factory()->create();
        Sanctum::actingAs($user);

        $form = Form::factory()->create();

        $response = $this->postJson('/api/responses/start', ['form_id' => $form->id]);

        $response->assertStatus(201)
                 ->assertJsonStructure(['id', 'form_id', 'session_token', 'submitted', 'created_at', 'updated_at']);

        $this->assertDatabaseHas('responses', ['form_id' => $form->id, 'submitted' => false]);
    }

    /**
     * Test that response progress can be saved.
     */
    public function test_response_progress_can_be_saved(): void
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        $form = Form::factory()->create();
        $question1 = Question::factory()->create(['form_id' => $form->id, 'type' => 'text']);
        $question2 = Question::factory()->create(['form_id' => $form->id, 'type' => 'number']);

        $responseSession = Response::factory()->create(['form_id' => $form->id]);

        $answers = [
            ['question_id' => $question1->id, 'answer' => ['value' => 'Test Answer']],
            ['question_id' => $question2->id, 'answer' => ['value' => 123]],
        ];

        $response = $this->postJson('/api/responses/' . $responseSession->session_token . '/save', ['answers' => $answers]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['question_id' => $question1->id, 'answer' => ['value' => 'Test Answer']])
                 ->assertJsonFragment(['question_id' => $question2->id, 'answer' => ['value' => 123]]);

        $this->assertDatabaseHas('response_answers', ['response_id' => $responseSession->id, 'question_id' => $question1->id, 'answer' => json_encode(['value' => 'Test Answer'])]);
        $this->assertDatabaseHas('response_answers', ['response_id' => $responseSession->id, 'question_id' => $question2->id, 'answer' => json_encode(['value' => 123])]);
    }

    /**
     * Test that a response with a file upload can be saved.
     */
    public function test_response_with_file_can_be_saved(): void
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        $form = Form::factory()->create();
        $question = Question::factory()->create(['form_id' => $form->id, 'type' => 'file']);

        $responseSession = Response::factory()->create(['form_id' => $form->id]);

        $file = UploadedFile::fake()->image('avatar.jpg');

        $answers = [
            ['question_id' => $question->id, 'file' => $file],
        ];

        $response = $this->postJson('/api/responses/' . $responseSession->session_token . '/save', ['answers' => $answers]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('response_answers', [
            'response_id' => $responseSession->id,
            'question_id' => $question->id,
        ]);

        $answer = ResponseAnswer::where('response_id', $responseSession->id)
                                ->where('question_id', $question->id)
                                ->first();
        $this->assertNotNull($answer);
        $answerContent = $answer->answer;
        $this->assertArrayHasKey('file_path', $answerContent);
        $this->assertArrayHasKey('original_name', $answerContent);
        $this->assertEquals('avatar.jpg', $answerContent['original_name']);
    }

    /**
     * Test that a response can be submitted.
     */
    public function test_response_can_be_submitted(): void
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        $form = Form::factory()->create();
        $question = Question::factory()->create(['form_id' => $form->id, 'required' => true]);
        $responseSession = Response::factory()->create(['form_id' => $form->id, 'submitted' => false]);
        ResponseAnswer::factory()->create([
            'response_id' => $responseSession->id,
            'question_id' => $question->id,
            'answer' => ['value' => 'Some answer']
        ]);

        $response = $this->postJson('/api/responses/' . $responseSession->session_token . '/submit');

        $response->assertStatus(200)
                 ->assertJsonFragment(['submitted' => true]);

        $this->assertDatabaseHas('responses', ['id' => $responseSession->id, 'submitted' => true]);
    }

    /**
     * Test that conditional logic is validated on submission.
     */
    public function test_conditional_logic_validation_on_submission(): void
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        $form = Form::factory()->create();
        $question1 = Question::factory()->create(['form_id' => $form->id, 'type' => 'boolean', 'question_text' => 'Do you like apples?']);
        $question2 = Question::factory()->create([
            'form_id' => $form->id,
            'type' => 'text',
            'question_text' => 'Why not?',
            'required' => true,
            'conditional_rules' => json_encode([
                'show_if' => [
                    'question_id' => $question1->id,
                    'operator' => 'equals',
                    'value' => false
                ]
            ])
        ]);

        $responseSession = Response::factory()->create(['form_id' => $form->id, 'submitted' => false]);

        // Case 1: Answer to question1 is true, so question2 should NOT be answered.
        ResponseAnswer::factory()->create([
            'response_id' => $responseSession->id,
            'question_id' => $question1->id,
            'answer' => ['value' => true]
        ]);
        ResponseAnswer::factory()->create([
            'response_id' => $responseSession->id,
            'question_id' => $question2->id,
            'answer' => ['value' => 'Because I hate them']
        ]);

        $response = $this->postJson('/api/responses/' . $responseSession->session_token . '/submit');
        $response->assertStatus(422)
                 ->assertJsonFragment(['message' => 'Conditional logic violation: Question ' . $question2->id . ' should not have been answered.']);

        // Reset and test Case 2: Answer to question1 is false, so question2 SHOULD be answered.
        $responseSession->answers()->delete(); // Clear previous answers
        $responseSession->update(['submitted' => false]);

        ResponseAnswer::factory()->create([
            'response_id' => $responseSession->id,
            'question_id' => $question1->id,
            'answer' => ['value' => false]
        ]);
        // Do NOT provide answer for question2

        $response = $this->postJson('/api/responses/' . $responseSession->session_token . '/submit');
        $response->assertStatus(422)
                 ->assertJsonFragment(['message' => 'Required question ' . $question2->id . ' not answered.']); // Assuming question2 is required implicitly by conditional logic

        // Reset and test Case 3: Answer to question1 is false, and question2 IS answered. (Valid)
        $responseSession->answers()->delete(); // Clear previous answers
        $responseSession->update(['submitted' => false]);

        ResponseAnswer::factory()->create([
            'response_id' => $responseSession->id,
            'question_id' => $question1->id,
            'answer' => ['value' => false]
        ]);
        ResponseAnswer::factory()->create([
            'response_id' => $responseSession->id,
            'question_id' => $question2->id,
            'answer' => ['value' => 'Because I prefer oranges']
        ]);

        $response = $this->postJson('/api/responses/' . $responseSession->session_token . '/submit');
        $response->assertStatus(200)
                 ->assertJsonFragment(['submitted' => true]);
    }

    /**
     * Test that a saved response can be retrieved.
     */
    public function test_saved_response_can_be_retrieved(): void
    {
        $user = \App\Models\User::factory()->create();
        Sanctum::actingAs($user);

        $form = Form::factory()->create();
        $question = Question::factory()->create(['form_id' => $form->id, 'type' => 'text']);
        $responseSession = Response::factory()->create(['form_id' => $form->id]);
        ResponseAnswer::factory()->create([
            'response_id' => $responseSession->id,
            'question_id' => $question->id,
            'answer' => ['value' => 'Retrieved Answer']
        ]);

        $response = $this->getJson('/api/responses/' . $responseSession->session_token);

        $response->assertStatus(200)
                 ->assertJsonFragment(['session_token' => $responseSession->session_token])
                 ->assertJsonFragment(['answer' => ['value' => 'Retrieved Answer']]);
    }
}