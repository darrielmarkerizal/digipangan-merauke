<?php

namespace Modules\Page\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['question', 'answer', 'sort_order', 'is_active'])]
class Faq extends Model implements AuditableContract
{
    use Auditable;

    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
