<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Response;

class Form extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description', 'json_schema'];

    protected $casts = [
        'json_schema' => 'array',
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function responses(): HasMany
    {
        return $this->hasMany(Response::class);
    }
}
