<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuditResource;
use App\Repositories\Contracts\AuditRepositoryInterface;
use App\Support\InertiaQuery;
use Inertia\Response;

class AuditLogAdminController extends Controller
{
    public function __construct(private readonly AuditRepositoryInterface $audits) {}

    public function index(): Response
    {
        return InertiaQuery::render(
            'Admin/AuditLog',
            $this->audits->paginateFiltered(),
            AuditResource::class,
            [],
            'audits'
        );
    }
}
