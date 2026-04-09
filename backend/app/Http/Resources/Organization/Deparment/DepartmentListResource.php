<?php

namespace App\Http\Resources\Organization\Deparment;

use App\Http\Resources\BaseListResource;
use Illuminate\Http\Request;

class DepartmentListResource extends BaseListResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @return array<string, mixed>
	 */
	public function toArray(Request $request): array
	{
		$parent = parent::toArray($request);

		$department = [
			'leader_id' => $this->leader_id,
		];

		return array_merge($parent, $department);
	}
}
