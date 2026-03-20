<?php

namespace App\Http\Resources\System;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PermissionCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->collection->groupBy('module')->map(function ($items) {
			return $items->map(function ($item) {
				return [
						'id' => $item->id,
                        'name' => $item->name,
                        'slug' => $item->slug,
						'supported_scopes' => $item->supported_scopes
				];
			})->values();	
		})->toArray();
    }
}
