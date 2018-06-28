<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\ResponseWrapper;
use AuthSDK;
require_once dirname(__DIR__).'./../../vendor/autoload.php';

class AuthenticateMiddleware
{
    private $responseWrapper;
    private $senderToMessageAdapter;

    public function __construct() {
        $this->responseWrapper = new ResponseWrapper();
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = $request->header('authorization');

        if ($token) {

            //setup the config file
            $config = new AuthSDK\Config();
            $config->setOrigin('digitalefactuur');
            $config->setOriginPublicKey('digitalefactuur', env('PUBLIC_KEY'));
            $tokenService = new AuthSDK\TokenService($config);
        
            try {
                if ($token = $tokenService->verifyAccessToken($token)) {
                    if ($token['exp'] > strtotime(date('Y-m-d'))) {
                        $request->merge(['payload' => $token]);
                        return $next($request);
                    }
                }
            }
            catch (\Exception $e) {
                return $this->responseWrapper->badRequest(array('message' => 'The given JWT token is invalid or expired' , 'code' => 'InvalidJWT'));
            }
        }
        return $this->responseWrapper->badRequest(array('message' => 'The required headers Authorization is missing or empty', 'code' => 'MissingHeaders'));
    }
}
