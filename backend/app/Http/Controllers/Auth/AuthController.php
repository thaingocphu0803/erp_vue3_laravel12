<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\CreatePasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\Auth\AuthService;
use App\Trait\AutoGenerate;
use App\Trait\FormatResponse;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class AuthController extends Controller
{
	use FormatResponse, AutoGenerate;

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

	public function verifyEmail(Request $request)
	{
		$resendVerifyUrl = $this->generateFrontendUrlWithParams(
			'resend-verification',
			$request->route()->parameters()
		);

		if (!$request->hasValidSignature()) {
			return redirect($resendVerifyUrl);
		}

		$user = $this->authService->find($request->route('id'));

		if (!hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
			return redirect($resendVerifyUrl);
		}

		if (!$user->hasVerifiedEmail()) {
			$user->markEmailAsVerified();
			event(new Verified($user));
		}

		$newPassswordUrl = $this->generateFrontendUrlWithParams(
			'new-password',
			$request->route()->parameters()
		);

		return redirect($newPassswordUrl);
	}

	public function createPassword(CreatePasswordRequest $request)
	{
		$data = $request->validated();

		if ($this->authService->createPassword($data) === false) {
			$message = 'auth.alert.error.createPassword';
			return $this->jsonResponse($message, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
		}

		$message = 'auth.alert.success.createPassword';
		return $this->jsonResponse($message, JsonResponse::HTTP_OK);
	}

	public function resendVerifyEmail(Request $request)
	{
		$data = $request->validate([
			'id' => 'required|integer|exists:users,id',
			'hash' => 'required|string',
		]);

		$user = $this->authService->find($data['id']);

		if (!$user || !hash_equals((string) $data['hash'], sha1($user->getEmailForVerification()))) {
			return $this->jsonResponse('auth.validate.verify.invalidToken', JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
		}

		if ($user->hasVerifiedEmail()) {
			return $this->jsonResponse('auth.alert.success.already_verified', JsonResponse::HTTP_OK);
		}

		$user->notify(new \App\Notifications\QueueVerifyEmail(true));

		return $this->jsonResponse('auth.alert.success.resend_email', JsonResponse::HTTP_OK);
	}
}
