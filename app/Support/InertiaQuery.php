<?php

namespace App\Support;

use App\Traits\ApiResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;
use Inertia\Response;

class InertiaQuery
{
    public static function render(
        string $component,
        LengthAwarePaginator $paginator,
        ?string $resourceClass = null,
        array $additionalProps = [],
        string $propName = 'products'
    ): Response {
        $request = request();

        $paginatedData = ApiResponse::formatPaginatedData($paginator, $resourceClass);

        $filters = array_merge(
            (array) $request->query('filter', []),
            [
                'search' => (string) $request->query('search', ''),
                'sort' => (string) $request->query('sort', ''),
            ]
        );

        return Inertia::render($component, array_merge([
            $propName => $paginatedData,
            'filters' => array_filter($filters, fn ($val) => $val !== '' && $val !== []),
        ], $additionalProps));
    }
}
