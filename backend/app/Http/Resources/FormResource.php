<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Ensure json_schema is an array of questions, and each question's options are arrays
        $jsonSchema = $this->json_schema; // This should already be an array due to $casts in Form model

        // If json_schema is not an array (e.g., if it's null or not cast correctly), handle it defensively
        if (!is_array($jsonSchema)) {
            $jsonSchema = [];
        }

        // Transform each question within the json_schema using QuestionResource
        $transformedQuestions = collect($jsonSchema)->map(function ($questionData) {
            // Create a temporary Question model instance to leverage its casting
            // This is a workaround if the nested JSON isn't automatically cast
            $question = new \App\Models\Question((array) $questionData);
            return new QuestionResource($question);
        });

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'json_schema' => $transformedQuestions, // Use the transformed questions
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}