<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\Controllers\MemberController;
use App\Services\Account\Registration\Native\Base as NativeBase;
use App\Services\Account\Registration\ThirdParty\Base as NativeThirdParty;
use App\Services\Account\Registration\RegistrationFactory;

class RegisterServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(NativeBase::class, function () {
            $type = substr(url()->full(), strrpos(url()->full(), '/') + 1);
            $class = RegistrationFactory::create($type);

            return $class;
        });
    }
}
