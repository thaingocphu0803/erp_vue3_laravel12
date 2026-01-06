<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->withHeaders([
        'Referer' => 'http://reze.crm.local:5173',
        'Accept' => 'application/json',
    ]);
    User::factory()->create([
        'name' => 'admin',
        'email' => 'admin@gmail.com',
        'password' => Hash::make('Admin123@@')
    ]);
});

describe('login', function () {
    it('login successfully', closure: function () {
        $response = $this->postJson('api/auth/login', [
            'email' => 'admin@gmail.com',
            'password' => 'Admin123@@',
        ]);

        $response->assertValid(['email', 'password']);

        $response->assertStatus(status: 200);

        $this->assertAuthenticated();
    });

    it('login failed, because input data invalid', function () {});
});
