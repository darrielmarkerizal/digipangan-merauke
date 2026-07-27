<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResponse
{
    protected function successResponse($data = null, string $message = 'Success', int $code = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data,
        ];

        return response()->json($response, $code);
    }

    protected function errorResponse(string $message = 'Error', int $code = 400, $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if (! is_null($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    public static function formatPaginatedData(LengthAwarePaginator $paginator, ?string $resourceClass = null): array
    {
        $items = method_exists($paginator, 'getCollection')
            ? $paginator->getCollection()
            : collect($paginator->items());

        $collection = $resourceClass
            ? $resourceClass::collection($items)->resolve()
            : $items;

        return [
            'data' => $collection,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'from' => $paginator->firstItem(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'to' => $paginator->lastItem(),
                'total' => $paginator->total(),
            ],
            'links' => [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ],
        ];
    }

    protected function paginatedResponse(LengthAwarePaginator $paginator, string $message = 'Success', int $code = 200, ?string $resourceClass = null): JsonResponse
    {
        $paginatedData = self::formatPaginatedData($paginator, $resourceClass);

        $response = array_merge([
            'success' => true,
            'message' => $message,
        ], $paginatedData);

        return response()->json($response, $code);
    }
}
