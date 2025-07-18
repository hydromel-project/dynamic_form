<?php

namespace Database\Factories;

use App\Models\ResponseAnswer;
use App\Models\Response;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ResponseAnswer>
 */
class ResponseAnswerFactory extends Factory
{
    protected $model = ResponseAnswer::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'response_id' => Response::factory(),
            'question_id' => Question::factory(),
            'answer' => json_encode($this->faker->word),
        ];
    }
}
