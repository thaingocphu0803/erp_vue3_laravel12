<?php

namespace App\Http\Resources\HumanceResource\Employee;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeePaginateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->credential->name ?? config('system.n_a'),
            'avatar' => $this->avatar ?? null,
            'code' => $this->code,
            'email' => $this->credential->email ?? config('system.n_a'),
            'department_name' => $this->department->name ?? config('system.n_a'),
            'position_name' => $this->position->name ?? config('system.n_a'),
            'created_by' => $this->creator->name ?? config('system.n_a'),
        ];
    }
}
