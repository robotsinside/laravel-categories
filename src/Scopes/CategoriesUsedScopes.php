<?php

namespace RobotsInside\Categories\Scopes;

use Illuminate\Database\Eloquent\Builder;

trait CategoriesUsedScopes
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUsedGte(Builder $builder, $value)
    {
        return $builder->where('count', '>=', $value);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUsedGt(Builder $builder, $value)
    {
        return $builder->where('count', '>', $value);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUsedLte(Builder $builder, $value)
    {
        return $builder->where('count', '<=', $value);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUsedLt(Builder $builder, $value)
    {
        return $builder->where('count', '<', $value);
    }
}
