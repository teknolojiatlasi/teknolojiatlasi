<?php

namespace Modules\Simulation\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SimulationStat extends Model
{
    protected $fillable = [
        'simulation_id',
        'views_count',
        'runs_count',
        'favorites_count',
        'shares_count',
        'last_viewed_at',
    ];

    protected $casts = [
        'views_count' => 'integer',
        'runs_count' => 'integer',
        'favorites_count' => 'integer',
        'shares_count' => 'integer',
        'last_viewed_at' => 'datetime',
    ];

    public function simulation(): BelongsTo
    {
        return $this->belongsTo(Simulation::class);
    }
}
