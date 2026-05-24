<?php

namespace Modules\Cv\Models;

use Illuminate\Database\Eloquent\Model;

class CvEducation extends Model
{
    protected $table = 'cv_educations'; // 👈 ZORUNLU
    protected $fillable = [
        'cv_id','school','degree','year','description','order'
    ];
}
