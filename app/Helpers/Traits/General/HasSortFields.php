<?php

namespace App\Helpers\Traits\General;

use App\Exceptions\CustomExceptions\LogicException;
use Illuminate\Database\Eloquent\Builder;

trait HasSortFields
{
    /**
     * Handle sort for query
     */
    public function scopeSort(Builder $query, array $allowedSort, array $sort): Builder
    {
        foreach ($sort as $key => $value) {

            if (! in_array($key, $allowedSort)) {

                throw new LogicException(__('exceptions.not_allowed_sort', ['attribute' => $key]), 403);
            }

            $query->orderBy($key, $value);
        }

        return $query;
    }
}
