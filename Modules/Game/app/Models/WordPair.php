<?php

namespace Modules\Game\Models;

use Illuminate\Database\Eloquent\Model;

class WordPair extends Model
{
    protected $table = 'game_word_pairs';

    protected $fillable = [
        'word',
        'meaning',
    ];
}
