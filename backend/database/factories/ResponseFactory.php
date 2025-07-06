<?php

namespace Database\Factories;

use App\Models\Response;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Response>
 */
class ResponseFactory extends Factory
{
    protected $model = Response::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'form_id' => \App\Models\Form::factory(),
            'session_token' => Str::random(60),
            'submitted' => false,
        ];
    }
}
