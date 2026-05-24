<?php

namespace Modules\Contact\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $table = 'contact_messages';

    protected $fillable = [
        'contact_full_name',
        'contact_email',
        'contact_subject',
        'contact_message',
        'contact_is_read',
        'contact_read_at',
        'contact_is_replied',
        'contact_replied_at',
        'contact_reply_subject',
        'contact_reply_message',
        'contact_replied_by_id',
        'contact_meta',
    ];

    protected $casts = [
        'contact_is_read' => 'bool',
        'contact_read_at' => 'datetime',
        'contact_is_replied' => 'bool',
        'contact_replied_at' => 'datetime',
        'contact_meta' => 'array',
    ];

    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('contact_is_read', false);
    }

    public function scopeReplied(Builder $query): Builder
    {
        return $query->where('contact_is_replied', true);
    }
}

