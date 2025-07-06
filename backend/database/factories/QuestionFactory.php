<?php

namespace Database\Factories;

use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Question>
 */
class QuestionFactory extends Factory
{
    protected $model = Question::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'form_id' => \App\Models\Form::factory(),
            'question_text' => $this->faker->sentence,
            'type' => $this->faker->randomElement(['text', 'number', 'boolean']),
            'options' => json_encode([]),
            'conditional_rules' => json_encode([]),
            'required' => $this->faker->boolean,
            'order' => $this->faker->numberBetween(1, 10),
        ];
    }
}
