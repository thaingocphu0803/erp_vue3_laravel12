<?php

namespace App\Providers;

use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Eloquent\Organization\DepartmentRepository;
use App\Repositories\Eloquent\Organization\PositionRepository;
use App\Repositories\Eloquent\Organization\RoleRepository;
use App\Repositories\Eloquent\System\LookupRepository;
use App\Repositories\Interfaces\BaseRepositoryInterface;
use App\Repositories\Interfaces\Organization\DepartmentRepositoryInterface;
use App\Repositories\Interfaces\Organization\PositionRepositoryInterface;
use App\Repositories\Interfaces\Organization\RoleRepositoryInterface;
use App\Repositories\Interfaces\System\LookupRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
		$this->app->bind(LookupRepositoryInterface::class, LookupRepository::class);

        $this->app->bind(BaseRepositoryInterface::class, BaseRepository::class);

		$this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);

		$this->app->bind(DepartmentRepositoryInterface::class, DepartmentRepository::class);

		$this->app->bind(PositionRepositoryInterface::class, PositionRepository::class);
	}

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
