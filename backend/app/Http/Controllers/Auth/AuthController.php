<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\Auth\AuthService;
use App\Trait\HasResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class AuthController extends Controller
{
	use HasResponse;

	public function __construct(
		protected AuthService $authService
	) {}

	public function login(LoginRequest $loginRequest)
	{
		$data =  $loginRequest->validated();

		if (!$this->authService->login($data)) {

			$message = 'auth.alert.error.incorrectAuth';

			return  $this->jsonResponse($message, JsonResponse::HTTP_UNAUTHORIZED);
		}

		$loginRequest->session()->regenerateToken();

		$user = $this->authService->me();

		$message = 'auth.alert.success.login';

		return $this->jsonResponse($message, JsonResponse::HTTP_OK, compact('user'));
	}

	public function me()
	{
		$user = $this->authService->me();

		$message = 'auth.alert.success.me';
		return $this->jsonResponse($message, JsonResponse::HTTP_OK, compact('user'));
	}

	public function logout(Request $request)
	{
		$this->authService->logout();
		$request->session()->invalidate();
		$request->session()->regenerateToken();

		$message = 'auth.alert.success.logout';
		return  $this->jsonResponse($message, JsonResponse::HTTP_OK);
	}

	public function verifyEmail()
	{
		dd(1);
	}
}
