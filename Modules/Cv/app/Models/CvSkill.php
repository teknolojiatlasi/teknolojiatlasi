<?php

namespace Modules\Cv\Models;

use Illuminate\Database\Eloquent\Model;

class CvSkill extends Model
{
    protected $fillable = [
        'cv_id','name','level','sort_order'
    ];
}
