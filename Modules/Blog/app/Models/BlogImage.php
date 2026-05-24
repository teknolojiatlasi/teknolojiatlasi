<?php
namespace Modules\Blog\Models;

use Illuminate\Database\Eloquent\Model;

class BlogImage extends Model
{
    protected $fillable = [
        'blog_id',
        'image_path',
    ];

    public function blog()
    {
        return $this->belongsTo(Blog::class);
    }
}
