<?php

namespace App\Providers;

use App\Models\Department;
use App\Models\Position;
use App\Observers\System\NestedObserver;
use Illuminate\Support\ServiceProvider;

class ObserveServiceProvider extends ServiceProvider
{
	/**
	 * Register services.
	 */
	public function register(): void
	{
		//
	}


	/**
	 * Bootstrap services.
	 */
	public function boot(): void
	{
		$observers = [
			Department::class => NestedObserver::class,

			Position::class => NestedObserver::class,
		];

		foreach ($observers as $model => $observer) {
			$model::observe($observer);
		}
	}
}
