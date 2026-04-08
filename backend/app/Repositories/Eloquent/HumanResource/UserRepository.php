<?php

namespace App\Repositories\Eloquent\HumanResource;

use App\Models\User;
use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Interfaces\HumanResource\UserRepositoryInterface;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
	/**
	 * Create a new class instance.
	 */
	public function __construct(User $model)
	{
		parent::__construct($model);
	}
}
