<?php

namespace Modules\Page\Services;

use App\Services\BaseService;
use Modules\Page\Repositories\Contracts\FaqRepositoryInterface;

class FaqService extends BaseService
{
    public function __construct(FaqRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
