<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\CreatePasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ResendVerifyEmailRequest;
use App\Notifications\QueueVerifyEmail;
use App\Services\Auth\AuthService;
use App\Trait\AutoGenerate;
use App\Trait\FormatResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

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
			return $this->exceptionResponse($message, Response::HTTP_UNAUTHORIZED);
		}

		$loginRequest->session()->regenerateToken();

		$user = $this->authService->me();

		$message = 'auth.alert.success.login';

		return $this->jsonResponse($message, Response::HTTP_OK, compact('user'));
	}

	public function me()
	{
		$user = $this->authService->me();

		$message = 'auth.alert.success.me';
		return $this->jsonResponse($message, Response::HTTP_OK, compact('user'));
	}

	public function logout(Request $request)
	{
		$this->authService->logout();
		$request->session()->invalidate();
		$request->session()->regenerateToken();

		$message = 'auth.alert.success.logout';
		return  $this->jsonResponse($message, Response::HTTP_OK);
	}

	public function verifyEmail(Request $request)
	{
		$resendVerifyUrl = $this->generateFrontendUrlWithParams('resend-verify-email', $request->route()->parameters());

		if (!$request->hasValidSignature()) {
			return redirect($resendVerifyUrl);
		}

		$user = $this->authService->find($request->route('id'));

		if (!$user) {
			$serverErrorUrl = $this->generateFrontendUrlWithParams('error', ['code' => Response::HTTP_INTERNAL_SERVER_ERROR]);
			return redirect($serverErrorUrl);
		}

		if (!hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
			return redirect($resendVerifyUrl);
		}

		$newPassswordUrl = $this->generateFrontendUrlWithParams('new-password', $request->route()->parameters());

		return redirect($newPassswordUrl);
	}

	public function createPassword(CreatePasswordRequest $createPasswordRequest)
	{
		$data = $createPasswordRequest->validated();
		$this->authService->createPassword($data);

		$createPasswordRequest->session()->regenerateToken();

		$message = 'auth.alert.success.createPassword';
		return $this->jsonResponse($message, Response::HTTP_OK);
	}

	public function resendVerifyEmail(ResendVerifyEmailRequest $resendVerifyEmailRequest)
	{
		$data = $resendVerifyEmailRequest->validated();

		$user = $this->authService->find($data['id']);

		$user->notify(new QueueVerifyEmail(true));

		return $this->jsonResponse('auth.alert.success.resendVerifyEmail', Response::HTTP_OK);
	}
}
