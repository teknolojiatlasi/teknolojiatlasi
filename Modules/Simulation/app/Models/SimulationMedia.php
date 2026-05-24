<?php

namespace Modules\Simulation\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SimulationMedia extends Model
{
    protected $fillable = [
        'simulation_id',
        'type',
        'disk',
        'path',
        'title',
        'alt_text',
        'sort_order',
        'meta',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'meta' => 'array',
    ];

    public function simulation(): BelongsTo
    {
        return $this->belongsTo(Simulation::class);
    }
}
