<?php

namespace App\Http\Resources\Organization\Role;

use App\Http\Resources\BaseListResource;
use Illuminate\Http\Request;

class RoleListResource extends BaseListResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @return array<string, mixed>
	 */
	public function toArray(Request $request): array
	{
		return parent::toArray($request);
	}
}
