<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use OwenIt\Auditing\Models\Audit;

class AuditLogAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Audit::with('user')->latest();
        
        if ($request->has('event') && $request->event) {
            $query->where('event', $request->event);
        }
        
        if ($request->has('auditable_type') && $request->auditable_type) {
            $query->where('auditable_type', 'like', "%{$request->auditable_type}%");
        }

        $audits = $query->paginate(20)->withQueryString()->through(function ($audit) {
            return [
                'id' => $audit->id,
                'event' => $audit->event,
                'auditable_type' => class_basename($audit->auditable_type),
                'auditable_id' => $audit->auditable_id,
                'old_values' => $audit->old_values,
                'new_values' => $audit->new_values,
                'url' => $audit->url,
                'ip_address' => $audit->ip_address,
                'user_agent' => $audit->user_agent,
                'created_at' => $audit->created_at->format('Y-m-d H:i:s'),
                'user' => $audit->user ? [
                    'id' => $audit->user->id,
                    'name' => $audit->user->name,
                ] : null,
            ];
        });

        return Inertia::render('Admin/AuditLog', [
            'audits' => $audits,
            'filters' => $request->only(['event', 'auditable_type']),
        ]);
    }
}
