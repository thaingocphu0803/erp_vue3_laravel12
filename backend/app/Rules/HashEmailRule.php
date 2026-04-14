<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class HashEmailRule implements ValidationRule
{
	public function __construct(
		protected int $id
	) {}
	/**
	 * Run the validation rule.
	 *
	 * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
	 */
	public function validate(string $attribute, mixed $value, Closure $fail): void
	{
		$user = DB::table('users')->where('id', $this->id)->first();

		if (!$user || !hash_equals((string) $value, sha1($user->email))) {
			$fail('auth.validate.verify.invalidToken');
		}
	}
}
