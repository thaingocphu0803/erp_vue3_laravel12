<?php

namespace App\Http\Resources\Organization\Position;

use App\Http\Resources\BaseListResource;
use Illuminate\Http\Request;

class PositionListResource extends BaseListResource
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
