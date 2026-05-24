<?php

namespace Modules\Simulation\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SimulationFavorite extends Model
{
    protected $fillable = [
        'simulation_id',
        'user_id',
    ];

    public function simulation(): BelongsTo
    {
        return $this->belongsTo(Simulation::class);
    }
}
