<?php

namespace Modules\Exam;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'parent_id',
        'order',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::deleting(function ($menu) {
            // Prevent deletion if menu has children
            if ($menu->hasChildren()) {
                throw new \Exception('Cannot delete menu with children.');
            }
        });
    }

    /**
     * Get the parent menu item.
     */
    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    /**
     * Get the child menu items.
     */
    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('order');
    }

    /**
     * Get the exams for this menu.
     */
    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    /**
     * Check if the menu has children.
     */
    public function hasChildren()
    {
        return $this->children()->count() > 0;
    }

    /**
     * Scope a query to only include parent menus.
     */
    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Get all ancestors of the menu item.
     */
    public function ancestors()
    {
        $ancestors = collect([]);

        $menu = $this->parent;
        while ($menu) {
            $ancestors->push($menu);
            $menu = $menu->parent;
        }

        return $ancestors;
    }

    /**
     * Get the breadcrumb trail for the menu item.
     */
    public function breadcrumbs()
    {
        $breadcrumbs = $this->ancestors()->reverse()->push($this);
        return $breadcrumbs;
    }
}