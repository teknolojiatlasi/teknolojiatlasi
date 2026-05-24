<?php
namespace Modules\Blog\Models;

use Illuminate\Database\Eloquent\Model;

class BlogCategory extends Model
{
    protected $fillable = [
        'parent_id',
        'name',
        'slug',
    ];

    public function parent()
    {
        return $this->belongsTo(BlogCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(BlogCategory::class, 'parent_id');
    }

    public function blogs()
    {
        return $this->hasMany(Blog::class);
    }
}
