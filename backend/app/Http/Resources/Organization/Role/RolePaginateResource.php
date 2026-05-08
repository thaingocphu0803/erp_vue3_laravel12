<?php

namespace App\Http\Resources\Organization\Role;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class RolePaginateResource extends JsonResource
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
			'name' => $this->name,
			'code' => $this->code,
			'users_count' => $this->users_count,
			'created_by' => $this->creator->name ?? config('system.n_a'),
			'status' => $this->status
		];
	}
}
