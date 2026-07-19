<?php

namespace App\Support\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class UniversalSearch implements Filter
{
    public function __construct(
        private readonly array $columns,
        private readonly int $maxTerms = 6,
        private readonly int $minTermLength = 2,
    ) {}

    public function __invoke(Builder $query, mixed $value, string $property): void
    {
        $terms = $this->terms($value);

        if ($terms === [] || $this->columns === []) {
            return;
        }

        $query->where(function (Builder $outer) use ($terms) {
            foreach ($terms as $term) {
                $outer->where(fn (Builder $inner) => $this->matchAnyColumn($inner, $term));
            }
        });

        if (request()->missing('sort')) {
            $this->orderByRelevance($query, $terms[0]);
        }
    }

    private function terms(mixed $value): array
    {
        $raw = is_array($value) ? implode(' ', $value) : (string) $value;

        return collect(preg_split('/\s+/u', trim($raw), -1, PREG_SPLIT_NO_EMPTY) ?: [])
            ->filter(fn (string $term) => mb_strlen($term) >= $this->minTermLength)
            ->take($this->maxTerms)
            ->values()
            ->all();
    }

    private function matchAnyColumn(Builder $query, string $term): void
    {
        $pattern = '%'.$this->escape($term).'%';

        foreach ($this->columns as $column) {
            if (str_contains($column, '.')) {
                $segments = explode('.', $column);
                $field = array_pop($segments);

                $query->orWhereHas(
                    implode('.', $segments),
                    fn (Builder $related) => $related->where($field, 'like', $pattern)
                );

                continue;
            }

            $query->orWhere($query->qualifyColumn($column), 'like', $pattern);
        }
    }

    private function orderByRelevance(Builder $query, string $term): void
    {
        $local = array_values(array_filter($this->columns, fn ($c) => ! str_contains($c, '.')));

        if ($local === []) {
            return;
        }

        $column = $query->qualifyColumn($local[0]);
        $escaped = $this->escape($term);

        $query->orderByRaw(
            "CASE WHEN {$column} LIKE ? THEN 0 WHEN {$column} LIKE ? THEN 1 ELSE 2 END",
            [$escaped, $escaped.'%']
        );
    }

    private function escape(string $term): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $term);
    }
}
