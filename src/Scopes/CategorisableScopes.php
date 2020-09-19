<?php

namespace RobotsInside\Categories\Scopes;

use Illuminate\Database\Eloquent\Builder;

trait CategorisableScopes
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder builder
     * @param array $categories
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithAnyCategory(Builder $builder, array $categories)
    {
        return $builder->hasCategories($categories);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder builder
     * @param array $categories
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithAllCategories(Builder $builder, array $categories)
    {
        foreach ($categories as $category) {
            $builder->hasCategories([$category]);
        }

        return $builder;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder builder
     * @param array $categories
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHasCategories(Builder $builder, array $categories)
    {
        return $builder->whereHas('categories', function ($query) use ($categories) {
            $query->whereIn('slug', $categories);
        });
    }
}
