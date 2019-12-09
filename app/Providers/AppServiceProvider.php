<?php

namespace App\Providers;

use App\Helpers\Topica;
use App\Http\Middleware\Active;
use App\Http\Repositories\Contracts\CDTRepositoryInterface;
use App\Http\Repositories\Contracts\CrossCheckInfoRepositoryInterface;
use App\Http\Repositories\Contracts\CrossCheckYearRepositoryInterface;
use App\Http\Repositories\Contracts\EmployeeOrderRepositoryInterface;
use App\Http\Repositories\Contracts\EmployeeRepositoryInterface;
use App\Http\Repositories\Contracts\EmpRentRepositoryInterface;
use App\Http\Repositories\Contracts\HDRepositoryInterface;
use App\Http\Repositories\Contracts\OrderRepositoryInterface;
use App\Http\Repositories\Contracts\PTRepositoryInterface;
use App\Http\Repositories\Contracts\SPRepositoryInterface;
use App\Http\Repositories\Contracts\SummaryRepositoryInterface;
use App\Http\Repositories\Contracts\TypeRepositoryInterface;
use App\Http\Repositories\Contracts\UserRepositoryInterface;
use App\Http\Repositories\Eloquents\CDTEloquent;
use App\Http\Repositories\Eloquents\CrossCheckInfoEloquent;
use App\Http\Repositories\Eloquents\CrossCheckYearEloquent;
use App\Http\Repositories\Eloquents\EmployeeEloquent;
use App\Http\Repositories\Eloquents\EmployeeOrderEloquent;
use App\Http\Repositories\Eloquents\EmpRentEloquent;
use App\Http\Repositories\Eloquents\HDEloquent;
use App\Http\Repositories\Eloquents\OrderEloquent;
use App\Http\Repositories\Eloquents\PTEloquent;
use App\Http\Repositories\Eloquents\SPEloquent;
use App\Http\Repositories\Eloquents\SummaryEloquent;
use App\Http\Repositories\Eloquents\TypeEloquent;
use App\Http\Repositories\Eloquents\UserEloquent;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\ServiceProvider;

use App\Http\Repositories\Contracts\RoleRepositoryInterface;
use App\Http\Repositories\Eloquents\RoleEloquent;

use App\Http\Repositories\Contracts\PermissionRepositoryInterface;
use App\Http\Repositories\Eloquents\PermissionEloquent;

use App\Http\Repositories\Contracts\CrossCheckRepositoryInterface;
use App\Http\Repositories\Eloquents\CrossCheckEloquent;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['router']->middlewareGroup('active', [Active::class]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //load helper
        foreach (glob(app_path() . '/Helpers/*.php') as $filename) {
            require_once($filename);
        }
        //
        $this->app->singleton(
            UserRepositoryInterface::class,
            UserEloquent::class
        );
        $this->app->singleton(
            OrderRepositoryInterface::class,
            OrderEloquent::class
        );
        $this->app->singleton(
            EmployeeOrderRepositoryInterface::class,
            EmployeeOrderEloquent::class
        );
        $this->app->singleton(
            EmployeeRepositoryInterface::class,
            EmployeeEloquent::class
        );

        $this->app->singleton(
            EmpRentRepositoryInterface::class,
            EmpRentEloquent::class
        );

        /*
        * Mange role
        */
        $this->app->singleton(
            RoleRepositoryInterface::class,
            RoleEloquent::class
        );

        $this->app->singleton(
            PermissionRepositoryInterface::class,
            PermissionEloquent::class
        );

        /**
         * Cross check
         */
        $this->app->singleton(
            CrossCheckRepositoryInterface::class,
            CrossCheckEloquent::class
        );
        /**
         * Cross check Info
         */
        $this->app->singleton(
            CrossCheckInfoRepositoryInterface::class,
            CrossCheckInfoEloquent::class
        );

        $this->app->singleton(
            SummaryRepositoryInterface::class,
            SummaryEloquent::class
        );

        $this->app->singleton(
            TypeRepositoryInterface::class,
            TypeEloquent::class
        );

        //CDT
        $this->app->singleton(
            CDTRepositoryInterface::class,
            CDTEloquent::class
        );

        //PT
        $this->app->singleton(
            PTRepositoryInterface::class,
            PTEloquent::class
        );

        //HD
        $this->app->singleton(
            HDRepositoryInterface::class,
            HDEloquent::class
        );

        //SP
        $this->app->singleton(
            SPRepositoryInterface::class,
            SPEloquent::class
        );


        // Trait
        $this->app->bind('topica', function () {
            return new Topica();
        });

        //Cross check year
        $this->app->singleton(
            CrossCheckYearRepositoryInterface::class,
            CrossCheckYearEloquent::class
        );

    }
}
