<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromptLibrary extends Model
{
    use HasFactory;

    protected $table = 'prompt_library';

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'category',
        'is_public',
        'use_count',
    ];

    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
            'use_count' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
