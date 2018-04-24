<?php

namespace AuthSDK;

use AuthSDK\Exceptions\AuthSDKException;


class TokenPair
{
    /**
     * @var Config
     */
    protected $config;

    function __construct(Config &$config)
    {
        $this->config = $config;
    }
    /**call to create a link to login for the given strategy
     *
     * @param string  $strategy the app you want to get data from (for example exact or google)
     * @param  string $authServerToken
     *
     * @return array
     * @throws AuthSDKException
     */
    public function createRetrieveTokenPairLink(string $strategy, string $authServerToken): array
    {
        try{
            $request = $this->config->getClient()->get(
                $this->config->getBaseUrl() . "/tokenPair/createLink/$strategy",
                [
                    'query'   => ['origin' => $this->config->getOrigin()],
                    'headers' => ['auth-server-token' => $authServerToken],
                ]
            );
            $response = $request->getBody()
                ->getContents();

            $parsedResponse = json_decode(
                $response,
                true
            );
        }catch (\Exception $e){
            throw new AuthSDKException(
                $e->getMessage(),
                $e->getCode()
            );
        }
        return $parsedResponse;
    }

    /** call to retrieve a new accessToken
     *
     *
     * @param string $strategy the app you want to get data from (for example exact or google)
     * @param array  $headers
     * @param string $authServerToken
     *
     * @return array
     *
     * @throws AuthSDKException
     */
    public function refreshTokenPair(string $strategy, array $headers, string $authServerToken): array
    {
        $baseHeaders = ['auth-server-token' => $authServerToken];
        $headers = array_merge(
            $headers,
            $baseHeaders
        );
        try{
            $request = $this->config->getClient()->get(
                $this->config->getBaseUrl() . "/tokenPair/provider/refreshToken/$strategy",
                [
                    'query'   => ['origin' => $this->config->getOrigin()],
                    'headers' => $headers,

                ]
            );

            $response = $request->getBody()
                ->getContents();

            $parsedResponse = json_decode(
                $response,
                true
            );
        }catch (\Exception $e){
            throw new AuthSDKException(
                $e->getMessage(),
                $e->getCode()
            );
        }
        return $parsedResponse;
    }
}
