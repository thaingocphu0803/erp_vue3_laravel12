<?php

namespace App\Services\HumanResource;

use App\Enum\PhoneCode;
use App\Repositories\Interfaces\HumanResource\EmployeeRepositoryInterface;
use App\Repositories\Interfaces\HumanResource\UserRepositoryInterface;
use App\Repositories\Interfaces\Organization\DepartmentRepositoryInterface;
use App\Services\System\FileService;
use App\Trait\AutoGenerate;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EmployeeService
{
	use AutoGenerate;
	public function __construct(
		protected UserRepositoryInterface $userRepository,
		protected EmployeeRepositoryInterface $employeeRepository,
		protected DepartmentRepositoryInterface $departmentRepositoryInterface,
		protected FileService $fileService
	) {}

	public function create(array $data)
	{
		$avatarPath = null;
		$user = null;

		if (!empty($data['avatar'])) {
			$filename = Str::uuid() . '.webp';
			$path = 'avatars';
			$avatarPath = $this->fileService->upload($data['avatar'], $path, $filename);
		}

		try {
			return DB::transaction(function () use ($data, $avatarPath) {
				$credentialPayload = $this->getCredentialPayload($data);
				$user = $this->userRepository->create($credentialPayload);

				$user->roles()->attach($data['role_ids']);

				$employeePayload = $this->getEmployeePayload($data, $user->id, $avatarPath);
				$this->employeeRepository->create($employeePayload);

				if ($data['is_leader']) {
					$departmentPayload = ['leader_id' => $user->id];
					$this->departmentRepositoryInterface->update($data['department_id'], $departmentPayload);
				}

				event(new Registered($user));

				return true;
			});
		} catch (\Exception $e) {
			$this->fileService->delete($avatarPath);
			return false;
		}
	}

	private function getCredentialPayload(array $data)
	{
		$password =  Str::password(8);

		return [
			'name' => $data['name'],
			'email' => $data['email'],
			'password' => $password,
			'locale' => $data['locale'],
		];
	}

	private function getEmployeePayload(array $data, int $userId, ?string $avatar)
	{

		$code = !is_null($data['code']) ? $data['code'] : $this->generateCode('employees', 'EMP');

		$phoneNumber = preg_replace('/^0/', PhoneCode::VIETNAM->value, $data['phone_number']);

		return [
			'user_id' => $userId,
			'code' => $code,
			'address' => $data['address'],
			'phone_number' => $phoneNumber,
			'gender' => $data['gender'],
			'birth_date' => $data['birth_date'],
			'avatar' => $avatar,
			'department_id' => $data['department_id'],
			'position_id' => $data['position_id'],
			'ward_code' => $data['ward_code'],
			'province_code' => $data['province_code'],
			'created_by' => Auth::id(),
		];
	}
}
