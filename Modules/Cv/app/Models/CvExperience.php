<?php

namespace Modules\Cv\Models;

use Illuminate\Database\Eloquent\Model;

class CvExperience extends Model
{
    protected $fillable = [
        'cv_id','company','position','start_date','end_date','description','sort_order'
    ];
}
