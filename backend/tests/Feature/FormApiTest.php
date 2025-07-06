<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Form;
use App\Models\User;

class FormApiTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * Test that a form can be created.
     */
    public function test_form_can_be_created(): void
    {
        $formData = [
            'name' => 'Test Form',
            'description' => 'This is a test form.',
            'json_schema' => json_encode(['questions' => []])
        ];

        $response = $this->actingAs($this->user)->postJson('/api/forms', $formData);

        $response->assertStatus(201)
                 ->assertJsonFragment(['name' => 'Test Form']);

        $this->assertDatabaseHas('forms', ['name' => 'Test Form']);
    }

    /**
     * Test that forms can be listed.
     */
    public function test_forms_can_be_listed(): void
    {
        Form::factory()->create(['name' => 'Form 1']);
        Form::factory()->create(['name' => 'Form 2']);

        $response = $this->actingAs($this->user)->getJson('/api/forms');

        $response->assertStatus(200)
                 ->assertJsonCount(2)
                 ->assertJsonFragment(['name' => 'Form 1'])
                 ->assertJsonFragment(['name' => 'Form 2']);
    }

    /**
     * Test that a single form can be retrieved.
     */
    public function test_single_form_can_be_retrieved(): void
    {
        $form = Form::factory()->create(['name' => 'Single Form']);

        $response = $this->actingAs($this->user)->getJson('/api/forms/' . $form->id);

        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => 'Single Form']);
    }

    /**
     * Test that a form can be updated.
     */
    public function test_form_can_be_updated(): void
    {
        $form = Form::factory()->create(['name' => 'Old Name']);

        $updatedData = [
            'name' => 'New Name',
            'description' => 'Updated description.',
            'json_schema' => json_encode(['questions' => ['new']])
        ];

        $response = $this->actingAs($this->user)->putJson('/api/forms/' . $form->id, $updatedData);

        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => 'New Name']);

        $this->assertDatabaseHas('forms', ['id' => $form->id, 'name' => 'New Name']);
    }

    /**
     * Test that a form can be deleted.
     */
    public function test_form_can_be_deleted(): void
    {
        $form = Form::factory()->create();

        $response = $this->actingAs($this->user)->deleteJson('/api/forms/' . $form->id);

        $response->assertStatus(204);

        $this->assertDatabaseMissing('forms', ['id' => $form->id]);
    }
}
