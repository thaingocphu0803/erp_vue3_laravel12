<?php

namespace App\Trait;

use Illuminate\Support\Facades\DB;

trait HasAutoCode
{
	public function generateCode($table, $prefix = 'AUTO', $length = 4)
	{
		$existCodes = DB::table($table)
			->select('code')
			->where('code', 'LIKE', $prefix . '%')
			->pluck('code')
			->toArray();

		if (!$existCodes) {
			return $prefix . str_pad('1', $length, '0', STR_PAD_LEFT);
		}

		$numbers = array_map(function ($code) use ($prefix) {
			$number = str_replace($prefix, '', $code);

			return is_numeric($number) ? (int) $number : 0;
		}, $existCodes);

		$newNumber = max($numbers) + 1;
		$newCode = $prefix . str_pad($newNumber, $length, '0', STR_PAD_LEFT);

		while (in_array($newCode, $existCodes)) {
			$newNumber++;
			$newCode =  $prefix . str_pad($newNumber, $length, '0', STR_PAD_LEFT);
		}

		return $newCode;
	}
}
