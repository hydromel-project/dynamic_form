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
        // Get the json_schema attribute. It should be an array due to $casts in Form model.
        // However, if it's still a string (e.g., direct database retrieval without model casting applied),
        // we need to decode it here.
        $jsonSchema = $this->json_schema;
        if (is_string($jsonSchema)) {
            $jsonSchema = json_decode($jsonSchema, true);
        }

        // Ensure jsonSchema is an array, even if it was null or invalid JSON
        if (!is_array($jsonSchema)) {
            $jsonSchema = [];
        }

        // Transform each question within the json_schema using QuestionResource
        $transformedQuestions = collect($jsonSchema)->map(function ($questionData) {
            // Explicitly decode options and conditional_rules if they are strings
            // This handles cases where nested JSON might not be automatically cast
            if (isset($questionData['options']) && is_string($questionData['options'])) {
                $questionData['options'] = json_decode($questionData['options'], true);
            }
            if (isset($questionData['conditional_rules']) && is_string($questionData['conditional_rules'])) {
                $questionData['conditional_rules'] = json_decode($questionData['conditional_rules'], true);
            }

            // Create a temporary Question model instance to leverage its casting (if any)
            // and then pass it to QuestionResource
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
