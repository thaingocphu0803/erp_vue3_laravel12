<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Trait\HandleResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\JsonResponse;

class AuthController extends Controller
{
    use HandleResponse;

    public function __construct() {}

    public function login(LoginRequest $loginRequest)
    {
        $credentials = $loginRequest->validated();

        $rememberMe = (bool) $loginRequest->input('rememberMe');

        $auth = Auth::attempt($credentials, $rememberMe);

        if ($auth) {
            $loginRequest->session()->regenerateToken();

            $message = 'auth.alert.success.login';
            $user = Auth::user()->only(['name', 'email']);
            $data['user'] = $user;
            return $this->jsonResponse($message, JsonResponse::HTTP_OK, $data);
        }

        $message = 'auth.alert.error.incorrectAuth';

        return  $this->jsonResponse($message, JsonResponse::HTTP_UNAUTHORIZED);
    }

    public function me()
    {
        $user = Auth::user();

        $user = Auth::user()->only(['name', 'email']);
        $data['user'] = $user;
        $message = 'auth.alert.success.me';
        return $this->jsonResponse($message, JsonResponse::HTTP_OK, $data);
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        $message = 'auth.alert.success.logout';

        return  $this->jsonResponse($message, JsonResponse::HTTP_OK);
    }
}
