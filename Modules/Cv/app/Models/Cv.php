<?php

namespace Modules\Cv\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Modules\Cv\Support\HtmlSanitizer;

class Cv extends Model
{
    protected $fillable = [
        'user_id',
        'full_name',
        'title',
        'email',
        'phone',
        'address',
        'photo',
        'about',
        'template',
    ];

    public function setAboutAttribute($value): void
    {
        $this->attributes['about'] = HtmlSanitizer::sanitize($value);
    }

    public function getPhotoUrlAttribute(): ?string
    {
        if (! $this->photo) {
            return null;
        }

        if (Str::startsWith($this->photo, ['http://', 'https://', '/'])) {
            return $this->photo;
        }

        return '/storage/' . ltrim(str_replace('\\', '/', $this->photo), '/');
    }

    public function getPhotoPublicPathAttribute(): ?string
    {
        if (! $this->photo) {
            return null;
        }

        return public_path(ltrim($this->photo_url, '/'));
    }

    public function experiences()
    {
        return $this->hasMany(CvExperience::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function educations()
    {
        return $this->hasMany(CvEducation::class);
    }

    public function skills()
    {
        return $this->hasMany(CvSkill::class);
    }
}
