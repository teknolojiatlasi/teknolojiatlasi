<?php

namespace Modules\Sossial\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostMedia extends Model
{
    use HasFactory;

    protected $table = 'sosial_post_media';

    protected $fillable = [
        'post_id',
        'type',
        'path',
        'url',
        'sort',
    ];

    protected $casts = [
        'sort' => 'integer',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id');
    }
}

