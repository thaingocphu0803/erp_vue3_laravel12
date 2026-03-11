<?php

use App\Models\Department;
use App\Models\User;

describe('index department', function () {
	beforeEach(function () {
		$user = User::first();

		$this->actingAs($user);

		Department::factory()->create([
			'name' => 'Accounting',
			'code' => 'ACC',
			'status' => 'A'
		]);

		Department::factory()->create([
			'name' => 'Human Resource',
			'code' => 'HRD',
			'status' => 'X'
		]);
	});

	it('get index successfully', function (array $data) {
		$response = $this->getJson('api/department/index?' . http_build_query($data));

		$response->assertStatus(200);

		if (!empty($data['search']) && $data['search'] == 'H') {
			expect($response->json('data'))->not()->toBeEmpty()->each(function ($item) use ($data) {
				expect(
					str_contains($item->value['name'], $data['search']) ||
						str_contains($item->value['code'], $data['search'])
				)->toBeTrue();
			});
		}

		if(!empty($data['sortKey']) && !empty($data['sortOrder']) && $data['sortOrder'] == 'desc'){
			expect($response->json('data')[0]['name'])
			->not()
			->toBeEmpty()
			->toStartWith('Human');
		}

		if(!empty($data['status']) && $data['status'] == 'X'){
			expect($response->json('data')[0]['name'])
			->not()
			->toBeEmpty()
			->toStartWith('Human');
		}

		$response->assertJsonStructure([
			'data' => ['*' => ['id', 'code', 'description', 'name', 'status']],
			'meta' => ['current_page', 'last_page', 'total'],
			'links'
		]);
	})->with('valid_filter_department_index');

	it('validate query param failed', function(array $data, array $expectedErrors){
		$response = $this->getJson('api/department/index?' . http_build_query($data));

		$response->assertStatus(422);

		$response->assertInvalid($expectedErrors);

	})->with('invalid_filter_department_index');
});
