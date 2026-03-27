<?php

namespace App\Rules;

use App\Enum\PermissionScope;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class PermissionItemRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
		foreach($value as $key => $val){
			if(!is_int($key) || is_null(PermissionScope::tryFrom($val))){
				$fail('role.validate.permissions.format');
			}
		}

		$permissionIds = array_keys($value);

		$result = DB::table($attribute)->whereIn('id',$permissionIds)->count();

		if($result !== count($permissionIds)){
			$fail('role.validate.permissions.exists');
		}
	}
}
