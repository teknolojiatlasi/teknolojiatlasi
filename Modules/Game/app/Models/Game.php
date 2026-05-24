<?php

namespace Modules\Game\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Game\Database\Factories\GameFactory;

class Game extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): GameFactory
    // {
    //     // return GameFactory::new();
    // }
}
