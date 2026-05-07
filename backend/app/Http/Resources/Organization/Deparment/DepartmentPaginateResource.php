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
			'leader'      => $this->leader?->name,
			'created_by'  => $this->creator->name,
			'created_at'  => Carbon::parse($this->created_at)->format('Y/m/d'),
			'status'      => $this->status,
		];
	}
}
