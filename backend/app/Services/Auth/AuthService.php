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

	public function login(array $data)
	{
		$credentials = [
			'email' => $data['email'],
			'password' => $data['password']
		];

		$rememberMe = $data['rememberMe'];

		$result = Auth::attempt($credentials, $rememberMe);
		return $result;
	}

	public function me()
	{
		$auth = collect(Auth::user())->only(['name', 'email'])->toArray();
		return $auth;
	}

	public function logout()
	{
		Auth::guard('web')->logout();
	}

	public function find(int $id)
	{
		try {
			$auth =  $this->userRepositoryInterface->find($id);

			return $auth;
		} catch (\Exception $e) {
			return false;
		}
	}

	public function createPassword(array $data)
	{
		try {
			return DB::transaction(function () use ($data) {
				$payload = $this->getPasswordPayload($data);
				return $this->userRepositoryInterface->update($data['id'], $payload);
			});
		} catch (\Exception $e) {
			return false;
		}
	}

	private function getPasswordPayload(array $data)
	{
		return [
			'password' => Hash::make($data['password']),
			'email_verified_at' => now()
		];
	}
}
