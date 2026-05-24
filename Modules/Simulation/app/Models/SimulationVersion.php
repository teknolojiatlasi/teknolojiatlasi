<?php

namespace Modules\Simulation\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SimulationVersion extends Model
{
    protected $fillable = [
        'simulation_id',
        'version',
        'html_code',
        'css_code',
        'js_code',
        'change_note',
        'created_by',
    ];

    public function simulation(): BelongsTo
    {
        return $this->belongsTo(Simulation::class);
    }
}
