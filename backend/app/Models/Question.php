<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    use HasFactory;
    protected $fillable = [
        'form_id',
        'question_text',
        'type',
        'options',
        'conditional_rules',
        'required',
        'order'
    ];

    protected $casts = [
        'options' => 'array',
        'conditional_rules' => 'array',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }
}
