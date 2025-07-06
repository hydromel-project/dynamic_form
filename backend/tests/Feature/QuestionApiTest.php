<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Form;
use App\Models\Question;
use App\Models\User;

class QuestionApiTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * Test that a question can be created.
     */
    public function test_question_can_be_created(): void
    {
        $form = Form::factory()->create();

        $questionData = [
            'question_text' => 'What is your favorite color?',
            'type' => 'text',
            'options' => json_encode(['red', 'blue', 'green']),
            'conditional_rules' => json_encode(['show_if' => ['question_id' => 1, 'operator' => 'equals', 'value' => 'yes']]),
            'required' => true,
            'order' => 1
        ];

        $response = $this->actingAs($this->user)->postJson('/api/forms/' . $form->id . '/questions', $questionData);

        $response->assertStatus(201)
                 ->assertJsonFragment(['question_text' => 'What is your favorite color?']);

        $this->assertDatabaseHas('questions', ['question_text' => 'What is your favorite color?']);
    }

    /**
     * Test that questions can be listed for a form.
     */
    public function test_questions_can_be_listed_for_a_form(): void
    {
        $form = Form::factory()->create();
        Question::factory()->create(['form_id' => $form->id, 'question_text' => 'Question 1']);
        Question::factory()->create(['form_id' => $form->id, 'question_text' => 'Question 2']);

        $response = $this->actingAs($this->user)->getJson('/api/forms/' . $form->id . '/questions');

        $response->assertStatus(200)
                 ->assertJsonCount(2)
                 ->assertJsonFragment(['question_text' => 'Question 1'])
                 ->assertJsonFragment(['question_text' => 'Question 2']);
    }

    /**
     * Test that a single question can be retrieved.
     */
    public function test_single_question_can_be_retrieved(): void
    {
        $form = Form::factory()->create();
        $question = Question::factory()->create(['form_id' => $form->id, 'question_text' => 'Single Question']);

        $response = $this->actingAs($this->user)->getJson('/api/questions/' . $question->id);

        $response->assertStatus(200)
                 ->assertJsonFragment(['question_text' => 'Single Question']);
    }

    /**
     * Test that a question can be updated.
     */
    public function test_question_can_be_updated(): void
    {
        $form = Form::factory()->create();
        $question = Question::factory()->create(['form_id' => $form->id, 'question_text' => 'Old Question']);

        $updatedData = [
            'question_text' => 'New Question',
            'type' => 'number',
            'required' => false
        ];

        $response = $this->actingAs($this->user)->putJson('/api/questions/' . $question->id, $updatedData);

        $response->assertStatus(200)
                 ->assertJsonFragment(['question_text' => 'New Question']);

        $this->assertDatabaseHas('questions', ['id' => $question->id, 'question_text' => 'New Question']);
    }

    /**
     * Test that a question can be deleted.
     */
    public function test_question_can_be_deleted(): void
    {
        $form = Form::factory()->create();
        $question = Question::factory()->create(['form_id' => $form->id]);

        $response = $this->actingAs($this->user)->deleteJson('/api/questions/' . $question->id);

        $response->assertStatus(204);

        $this->assertDatabaseMissing('questions', ['id' => $question->id]);
    }
}
