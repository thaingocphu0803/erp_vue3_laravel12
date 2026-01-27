<?php

describe('remember me', function () {
	it('remembers user when rememberMe is true', function (array $data) {
		$response = $this->postJson('api/auth/login', $data);

		$response->assertStatus(200);

		$response->assertCookieNotExpired(Auth::guard('web')->getRecallerName());

		$this->assertAuthenticated();
	})->with('credential_with_rememberMe');

	it('does not remember user when rememberMe is false', function (array $data) {
		$response = $this->postJson('api/auth/login', $data);

		$response->assertStatus(200);

		$response->assertCookieMissing(Auth::guard('web')->getRecallerName());

		$this->assertAuthenticated();
	})->with('credential_without_rememberMe');
});
