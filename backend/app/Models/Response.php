<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;

class Response extends Model
{
    use HasFactory;

    protected $fillable = ['form_id', 'user_id', 'session_token', 'submitted'];

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(ResponseAnswer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}