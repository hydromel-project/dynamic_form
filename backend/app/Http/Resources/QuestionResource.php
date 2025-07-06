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
        return [
            'id' => $this->id,
            'question_text' => $this->question_text,
            'type' => $this->type,
            'options' => $this->options, // This will be automatically cast to array due to $casts in Model
            'conditional_rules' => $this->conditional_rules, // This will be automatically cast to array due to $casts in Model
            'required' => (bool) $this->required,
            'order' => (int) $this->order,
            'form_id' => (int) $this->form_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}