<?php

namespace Modules\Contact\Models;

use Illuminate\Database\Eloquent\Model;

class ContactSetting extends Model
{
    protected $table = 'contact_settings';

    protected $fillable = [
        'contact_company_name',
        'contact_address',
        'contact_city',
        'contact_district',
        'contact_phone',
        'contact_email',
        'contact_map_provider',
        'contact_lat',
        'contact_lng',
        'contact_updated_by_id',
    ];

    protected $casts = [
        'contact_lat' => 'float',
        'contact_lng' => 'float',
    ];

    public static function singleton(): self
    {
        return static::query()->first() ?? static::query()->create([
            'contact_company_name' => config('app.name', 'Firma'),
            'contact_map_provider' => 'osm',
        ]);
    }

    public function displayAddress(): string
    {
        $parts = array_filter([
            $this->contact_address,
            $this->contact_district,
            $this->contact_city,
        ]);

        return implode(', ', $parts);
    }
}

