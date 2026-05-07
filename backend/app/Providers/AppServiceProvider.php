<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
	/**
	 * Register any application services.
	 */
	public function register(): void
	{
		//
	}

	/**
	 * Bootstrap any application services.
	 */
	public function boot(): void
	{
		// Throw exception if missing attributes in local environment
		Model::preventSilentlyDiscardingAttributes(!$this->app->environment('production'));

		// Throw exception if lazyLoading in local environment
		Model::preventLazyLoading(!$this->app->environment('production'));

		// Rate limiter for retry
		RateLimiter::for('retry', function ($request) {
			$identifier = $request->user()->id ?? $request->ip();
			return Limit::perMinute(5, 1)->by($identifier . $request->path());
		});

		// Rate limiter for bulk action
		RateLimiter::for('bulk-action', function ($request) {
			$identifier = $request->user()->id ?? $request->ip();
			return Limit::perMinute(10, 1)->by($identifier . $request->path());
		});

		// Rate limiter for pagination
		RateLimiter::for('index', function ($request) {
			$identifier = $request->user()->id ?? $request->ip();
			return Limit::perMinute(20, 1)->by($identifier . $request->path());
		});
	}
}
