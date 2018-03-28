<?php

namespace App\Providers;

use App\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
require_once dirname(__DIR__).'./../vendor/autoload.php';

class AuthServiceProvider extends ServiceProvider
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
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

        $this->app['auth']->viaRequest('api', function ($request) {

            $header = $request->header('Authorization');

            if ($header) {

                //setup the config file
                $config = new \AuthSDK\Config();
                $config->setOrigin('digitalefactuur');
                //give guzzle client and config file to tokenpai when creating it
                $tokenService = new \AuthSDK\TokenService($config);

            
                if ($token = $tokenService->verifyAccessToken($header, str_replace('\\n', "\n", env('PUBLIC_KEY')))) {
                    //Use for dev only
                    define("ORIGIN_NAME", $token['aud'][0]);

                    // define("ORIGIN_NAME", $token['origin']);
                    return true;
                }
                return null;
            }
            return null;
        });
    }
}
