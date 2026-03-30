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
		return Auth::attempt($credentials, $rememberMe);
	}

	public function me(){
		return Auth::user()->only(['name','email']);
	}

	public function logout(){
        Auth::guard('web')->logout();
	}
}
