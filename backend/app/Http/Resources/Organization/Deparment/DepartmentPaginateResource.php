<?php

namespace App\Http\Resources\Organization\Deparment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class DepartmentPaginateResource extends JsonResource
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
			'code'        => $this->code,
			'users_count' => $this->users_count,
			'parent'      => $this->parent->name ?? config('system.n_a'),
			'leader'      => $this->leader->name ?? config('system.n_a'),
			'leader_id'   => $this->leader_id,
			'created_by'  => $this->creator->name ?? config('system.n_a'),
			'status'      => $this->status,
		];
	}
}
