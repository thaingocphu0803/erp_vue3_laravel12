<?php

namespace App\Services\Auth;

use App\Repositories\Interfaces\HumanResource\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthService
{
	/**
	 * Create a new class instance.
	 */
	public function __construct(
		protected UserRepositoryInterface $userRepositoryInterface
	) {}

	// login
	public function login(array $data)
	{
		$credentials = $this->getCredentials($data);

		$rememberMe = $data['rememberMe'];

		$result = Auth::attempt($credentials, $rememberMe);
		return $result;
	}

	// get me
	public function me()
	{
		$auth = collect(Auth::user())->only(['name', 'email'])->toArray();
		return $auth;
	}

	// logout
	public function logout()
	{
		Auth::guard('web')->logout();
	}

	// find user by id
	public function find(int $id)
	{
		$auth =  $this->userRepositoryInterface->find($id);
		return $auth;
	}

	// create password
	public function createPassword(array $data)
	{
		return DB::transaction(function () use ($data) {
			$payload = $this->getPasswordPayload($data);
			return $this->userRepositoryInterface->update($data['id'], $payload);
		});
	}

	// get password payload
	private function getPasswordPayload(array $data)
	{
		return [
			'password' => Hash::make($data['password']),
			'email_verified_at' => now()
		];
	}

	// get credentials
	private function getCredentials(array $data)
	{
		return [
			'email' => $data['email'],
			'password' => $data['password']
		];
	}
}
