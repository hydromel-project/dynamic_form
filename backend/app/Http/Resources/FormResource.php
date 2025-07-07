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
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'json_schema' => QuestionResource::collection($this->whenLoaded('questions')), // Use the questions relationship
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
