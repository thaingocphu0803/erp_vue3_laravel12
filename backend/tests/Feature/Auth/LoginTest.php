<?php

describe('login', function () {
	// login success case
	it('login successfully', function (array $data) {
		$response = $this->postJson('api/auth/login', $data);

		$response->assertValid(['email', 'password']);

		$response->assertStatus(status: 200);

		$this->assertAuthenticated();
	})->with('correct_credential');

	// validation  input failed case
	it('validation input failed', function (array $data, array $expectedErrors) {
		$response = $this->postJson('api/auth/login', $data);

		$response->assertStatus(422);

		$response->assertInvalid($expectedErrors);

		$this->assertGuest();
	})->with('invalid_input_data');

	// login failed case
	it('login failed', function (array $data) {
		$response = $this->postJson('api/auth/login', $data);
		$response->assertStatus(401);
		$this->assertGuest();
	})->with('incorrect_credential');
});
