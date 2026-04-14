<?php

namespace App\Trait;

use Illuminate\Support\Facades\DB;

trait AutoGenerate
{
	public function generateCode($table, $prefix = 'AUTO', $length = 4)
	{
		$lastCode = DB::table($table)
			->where('code', 'LIKE', $prefix . '%')
			->whereRaw('code REGEXP ?', ["^{$prefix}[0-9]+$"])
			->orderByRaw('CAST(SUBSTRING(code, ?) AS UNSIGNED) DESC', [strlen($prefix) + 1])
			->lockForUpdate()
			->value('code');

		if (!$lastCode) {
			return $prefix . str_pad('1', $length, '0', STR_PAD_LEFT);
		}

		$lastNumber = (int) filter_var($lastCode, FILTER_SANITIZE_NUMBER_INT);
		$newNumber = abs($lastNumber) + 1;

		return $prefix . str_pad($newNumber, $length, '0', STR_PAD_LEFT);
	}

	public function generateFrontendUrlWithParams(string $path, $params = [])

	{
		$url = config('app.frontend_url') . '/' . $path . '?';

		if (empty($params))  return $url;

		foreach ($params as $key => $value) {
			$url .= "{$key}={$value}&";
		}
		return rtrim($url, '&');
	}
}
