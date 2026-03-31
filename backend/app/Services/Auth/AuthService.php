<?php

namespace App\Services\Auth;

use Illuminate\Support\Facades\Auth;

class AuthService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

	public function login( array $credentials, bool $rememberMe ){
		$result = Auth::attempt($credentials, $rememberMe);
		return $result;
	}

	public function me(){
		$auth = collect(Auth::user())->only(['name','email'])->toArray();
		return $auth;
	}

	public function logout(){
        Auth::guard('web')->logout();
	}
}
