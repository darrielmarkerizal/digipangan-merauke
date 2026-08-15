<?php

namespace Modules\User\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'is_active' => $this->is_active,
            'avatar_url' => $this->avatarUrl(),
            'region_id' => $this->region_id,
            'region' => $this->whenLoaded('region', fn () => $this->region ? [
                'id' => $this->region->id,
                'name' => $this->region->name,
                'slug' => $this->region->slug,
            ] : null),
            'roles' => $this->whenLoaded('roles', fn () => $this->roles->pluck('name')),
            'permissions' => $this->when(
                $request->routeIs('api.auth.profile*'),
                fn () => $this->getAllPermissions()->pluck('name')
            ),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
