<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
      // $this->registerPolicies();
      //
      // Passport::routes();
      //
      // Passport::personalAccessClientId('client-id');

      Schema::defaultStringLength(191);
    }
}
