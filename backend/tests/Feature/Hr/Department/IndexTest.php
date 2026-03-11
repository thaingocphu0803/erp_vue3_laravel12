<?php

use App\Models\User;

describe('index department', function(){
	beforeEach(function(){
		$user = User::first();

		$this->actingAs($user);
	});

	it('get index successfully',function(array $data){

	});
});
