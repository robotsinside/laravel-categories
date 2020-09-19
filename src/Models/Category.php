<?php

namespace RobotsInside\Categories\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use RobotsInside\Categories\Scopes\CategoriesUsedScopes;

class Category extends Model
{
    use CategoriesUsedScopes;

    public $fillable = ['name', 'slug'];

    /**
     * Resolve a single category.
     *
     * @param string $name
     * @return RobotsInside\Categories\Models\Category
     */
    public function resolve($name)
    {
        return $this->firstOrCreate(['name' => $name, 'slug' => Str::slug($name)]);
    }

    /**
     * Resolve on ore more categories.
     *
     * @param string|array|Illuminate\Support\Collection $categories
     * @return Illuminate\Support\Collection
     */
    public function resolveAll($categories)
    {
        if (is_array($categories)) {
            $categories = collect($categories);
        } elseif (is_string($categories)) {
            $categories = collect([$categories]);
        }

        return $categories->map(function ($category) {
            return $this->resolve($category);
        });
    }
}
