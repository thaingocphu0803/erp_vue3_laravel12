<?php

namespace App\Providers;

use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Eloquent\Organization\DepartmentRepository;
use App\Repositories\Eloquent\Organization\PositionRepository;
use App\Repositories\Eloquent\Organization\RoleRepository;
use App\Repositories\Eloquent\System\AdministrativeUnitRepository;
use App\Repositories\Eloquent\System\LookupRepository;


use App\Repositories\Interfaces\BaseRepositoryInterface;
use App\Repositories\Interfaces\Organization\DepartmentRepositoryInterface;
use App\Repositories\Interfaces\Organization\PositionRepositoryInterface;
use App\Repositories\Interfaces\Organization\RoleRepositoryInterface;
use App\Repositories\Interfaces\System\AdministrativeUnitRepositoryInterface;
use App\Repositories\Interfaces\System\LookupRepositoryInterface;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
	/**
	 * Register services.
	 */
	public function register(): void
	{
		$bindings = [
			BaseRepositoryInterface::class => BaseRepository::class,

			LookupRepositoryInterface::class => LookupRepository::class,

			AdministrativeUnitRepositoryInterface::class => AdministrativeUnitRepository::class,

			RoleRepositoryInterface::class => RoleRepository::class,

			DepartmentRepositoryInterface::class => DepartmentRepository::class,

			PositionRepositoryInterface::class => PositionRepository::class,
		];

		foreach ($bindings as $interface => $repository) {
			$this->app->bind($interface, $repository);
		}
	}

	/**
	 * Bootstrap services.
	 */
	public function boot(): void
	{
		//
	}
}
