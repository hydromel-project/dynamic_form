<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $options = $this->options; // Get the options attribute

        // Explicitly decode options if it's a string (should be handled by model casting, but as a fallback)
        if (is_string($options)) {
            $options = json_decode($options, true);
        }

        $conditionalRules = $this->conditional_rules; // Get the conditional_rules attribute

        // Explicitly decode conditional_rules if it's a string (as a fallback)
        if (is_string($conditionalRules)) {
            $conditionalRules = json_decode($conditionalRules, true);
        }

        return [
            'id' => $this->id,
            'question_text' => $this->question_text,
            'type' => $this->type,
            'options' => $options,
            'conditional_rules' => $conditionalRules,
            'required' => (bool) $this->required,
            'order' => (int) $this->order,
            'form_id' => (int) $this->form_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
