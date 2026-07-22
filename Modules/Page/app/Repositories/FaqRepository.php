<?php

namespace Modules\Page\Repositories;

use App\Repositories\BaseRepository;
use Modules\Page\Models\Faq;
use Modules\Page\Repositories\Contracts\FaqRepositoryInterface;
use Spatie\QueryBuilder\AllowedFilter;

class FaqRepository extends BaseRepository implements FaqRepositoryInterface
{
    public function __construct(Faq $model)
    {
        parent::__construct($model);
    }

    protected function allowedFilters(): array
    {
        return [
            AllowedFilter::partial('question'),
            AllowedFilter::exact('is_active'),
        ];
    }

    protected function searchable(): array
    {
        return ['question', 'answer'];
    }

    protected function allowedSorts(): array
    {
        return ['sort_order', 'created_at'];
    }

    protected function defaultSort(): string
    {
        return 'sort_order';
    }
}
