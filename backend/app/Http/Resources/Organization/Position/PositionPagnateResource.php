<?php

namespace App\Http\Resources\Organization\Position;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PositionPagnateResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @return array<string, mixed>
	 */
	public function toArray(Request $request): array
	{
		return [
			'id'          => $this->id,
			'name'        => $this->name,
			'code' => $this->code,
			'department_name' => $this->department->name ?? config('system.n_a'),
			'parent_name' => $this->parent->name ?? config('system.n_a'),
			'created_by' => $this->creator->name ?? config('system.n_a'),
			'users_count' => $this->users_count,
			'status'      => $this->status,
		];
	}
}
