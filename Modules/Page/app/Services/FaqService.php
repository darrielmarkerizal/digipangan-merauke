<?php

namespace Modules\Page\Services;

use App\Services\BaseService;
use Illuminate\Database\Eloquent\Collection;
use Modules\Page\Repositories\Contracts\FaqRepositoryInterface;

class FaqService extends BaseService
{
    public function __construct(private readonly FaqRepositoryInterface $faqs)
    {
        parent::__construct($faqs);
    }

    public function publicActive(): Collection
    {
        return $this->faqs->publicActive();
    }
}
