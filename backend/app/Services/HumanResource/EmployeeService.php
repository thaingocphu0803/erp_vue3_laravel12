<?php

namespace App\Services\HumanResource;

use App\Enum\PhoneCode;
use App\Repositories\Interfaces\HumanResource\EmployeeRepositoryInterface;
use App\Repositories\Interfaces\HumanResource\UserRepositoryInterface;
use App\Repositories\Interfaces\Organization\DepartmentRepositoryInterface;
use App\Trait\HasAutoGenerate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EmployeeService
{
	use HasAutoGenerate;
	public function __construct(
		protected UserRepositoryInterface $userRepository,
		protected EmployeeRepositoryInterface $employeeRepository,
		protected DepartmentRepositoryInterface $departmentRepositoryInterface
	) {}

	public function create(array $data)
	{
		$avatar = 'url';
		dd($data);
		// try {
		// 	return DB::transaction(function () use ($data, $avatar) {
		// 		$credentialPayload = $this->getCredentialPayload($data);
		// 		$user = $this->userRepository->create($credentialPayload);

		// 		$user->roles()->attach($data['role_ids']);

		// 		$employeePayload = $this->getEmployeePayload($data, $user->id, $avatar);
		// 		$this->employeeRepository->create($employeePayload);

		// 		if ($data['is_leader']) {
		// 			$departmentPayload = ['leader_id' => $user->id];
		// 			$this->departmentRepositoryInterface->update($data['department_id'], $departmentPayload);
		// 		}
		// 	});
		// } catch (\Exception $e) {
		// 	return false;
		// }
	}

	private function getCredentialPayload(array $data)
	{
		$password =  Str::password(8);

		return [
			'name' => $data['name'],
			'email' => $data['email'],
			'password' => $password,
		];
	}

	private function getEmployeePayload(array $data, int $userId, string $avatar)
	{

		$code = !is_null($data['code']) ? $data['code'] : $this->generateCode('employees', 'EMP');

		$phoneNumber = preg_replace('/^0/', PhoneCode::VIETNAM->value, $data['phone_number']);

		return [
			'user_id' => $userId,
			'code' => $code,
			'address' => $data['address'],
			'phone_number' => $phoneNumber,
			'gender' => $data['gender'],
			'birthdate' => $data['birthdate'],
			'avatar' => $avatar,
			'department_id' => $data['department_id'],
			'position_id' => $data['position_id'],
			'is_leader' => $data['is_leader'],
			'name' => $data['name'],
			'ward_code' => $data['ward_code'],
			'province_code' => $data['province_code'],
			'created_by' => Auth::id(),
		];
	}
}
