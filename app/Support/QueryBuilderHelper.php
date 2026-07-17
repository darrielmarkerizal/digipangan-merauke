<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\AllowedFilter;

/**
 * Preset AllowedFilter yang sering dipakai lintas modul.
 * Dipakai di dalam Repository / Service saat bikin QueryBuilder.
 */
class QueryBuilderHelper
{
    /**
     * Case-insensitive LIKE filter di satu atau lebih kolom.
     *
     * @param  string|array<int, string>  $columns
     */
    public static function search(string $filterName, string|array $columns): AllowedFilter
    {
        $columns = (array) $columns;

        return AllowedFilter::callback($filterName, function (Builder $query, $value) use ($columns) {
            $query->where(function (Builder $q) use ($columns, $value) {
                foreach ($columns as $column) {
                    $q->orWhere($column, 'like', "%{$value}%");
                }
            });
        });
    }

    public static function dateRange(string $filterName, string $column): AllowedFilter
    {
        return AllowedFilter::callback($filterName, function (Builder $query, $value) use ($column) {
            [$from, $to] = array_pad(explode(',', (string) $value), 2, null);

            if ($from) {
                $query->whereDate($column, '>=', $from);
            }
            if ($to) {
                $query->whereDate($column, '<=', $to);
            }
        });
    }

    public static function boolean(string $filterName, string $column): AllowedFilter
    {
        return AllowedFilter::callback($filterName, function (Builder $query, $value) use ($column) {
            $query->where($column, filter_var($value, FILTER_VALIDATE_BOOLEAN));
        });
    }
}
