<?php

namespace RobotsInside\Categories\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use InvalidArgumentException;

class Categorisable extends Model
{
    /**
     * Get the categorised model.
     *
     * @return Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function categorisable()
    {
        return $this->morphTo();
    }

    /**
     * Constrain categories based on a time interval.
     *
     * @param \Illuminate\Database\Eloquent\Builder builder
     * @param string $timeframe The period defining the recency e.g. 7 d (7 days), 1 m (one month), 2 y (2 years)
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCategorisedWithin(Builder $builder, $timeframe)
    {
        [$period, $unit] = explode(' ', $timeframe);

        if (!is_numeric($period)) {
            throw new InvalidArgumentException('$timeframe must follow structure "period unit"');
        }

        if (!in_array($unit, ['day', 'days', 'month', 'months', 'year', 'years'])) {
            throw new InvalidArgumentException('$timeframe must follow structure "period unit"');
        }

        $unit = 'sub' . ucfirst(Str::plural($unit));

        return $builder->where('created_at', '>=', now()->{$unit}($period))
            ->where('created_at', '<=', now());
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder builder
     * @param string $type
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeType(Builder $builder, $type)
    {
        return $builder->where('categorisable_type', $type);
    }
}
