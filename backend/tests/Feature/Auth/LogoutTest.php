<?php

use App\Models\User;

describe('logout', function () {
    it('logout successfully', function () {

        $user = User::first();

        $response = $this->actingAs($user)->getJson('api/auth/logout');

        $response->assertStatus(200);

        $this->assertGuest('web');
    });

    it('unauthorized users cannot logout' , function () {
        $response = $this->getJson('api/auth/logout');

        $response->assertStatus(401);

        $this->assertGuest('web');
    });
});
