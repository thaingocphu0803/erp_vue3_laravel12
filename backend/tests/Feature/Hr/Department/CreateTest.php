<?php

use App\Models\Department;
use App\Models\User;

describe('create department', function () {

	beforeEach(function(){
		$user = User::first();
		$this->actingAs($user);

		Department::factory()->create([
			'name' => 'Accounting',
			'code' => 'ACC',
			'status' => 'A',
			'parent_id' => '1',
		]);
	});

	it('create successfully', function (array $data) {

		$response = $this->postJson('api/department/create', $data);

		$response->assertValid(['name', 'code', 'parent_id']);

		$response->assertStatus(200);


		$checkData=  [ 'name' => $data['name']];

		if(!is_null($data['code'])){
			$checkData['code'] = $data['code'];
		}

		$this->assertDatabaseHas('departments',$checkData);

		if(is_null($data['code'])){
			$department = Department::where('name', $data['name'])->first();
			expect($department->code)->not()->toBeEmpty()->toStartWith('DEP');
		}

	})->with('valid_department_create');


	it('validation input failed', function(array $data, array $expectedErrors){
		$response = $this->postJson('api/department/create', $data);

		$response->assertStatus(422);

		$response->assertInvalid($expectedErrors);
	})->with('invalid_department_create');
});
