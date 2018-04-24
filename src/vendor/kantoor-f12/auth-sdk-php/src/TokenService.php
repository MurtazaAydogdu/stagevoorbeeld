<?php
/**
 * Created by PhpStorm.
 * User: kelvin
 * Date: 19/03/18
 * Time: 10:11
 */

namespace AuthSDK;


use AuthSDK\Exceptions\AuthSDKException;
use Carbon\Carbon;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\ValidationData;
use Lcobucci\JWT\Signer\Rsa\Sha256;

class TokenService
{
    /**
     * @var Config
     */
    protected $config;

    function __construct(Config &$config)
    {
        $this->config = $config;
    }

    /** create an authServerToken which is needed for most calls with the auth server
     *
     * @param string|null $tokenPairApp
     *
     * @return string
     */
    public function createAuthServerToken(string $tokenPairApp = null): string
    {
        $builder = new Builder();
        $builder->set(
            'aud',
            ['Authentication Server']
        );
        $builder->setIssuer($this->config->getOrigin());
        $dt = Carbon::now();
        $builder->setIssuedAt($dt->getTimestamp());
        $dt->addDay(30);
        $builder->setExpiration($dt->getTimestamp());
        if ($tokenPairApp !== null) {
            $builder->set(
                'tokenPairApp',
                $tokenPairApp
            );
        }

        $signer = new Sha256();
        $builder = $builder->sign(
            $signer,
            $this->config->getAuthServerTokenPrivateKey()
        );
        return $builder->getToken()
            ->__toString();
    }

    /** parse and verify the accessToken
     *
     * @param string $token    the raw AccessToken
     * @param string $signKeys the public key to validate the token
     *
     * @return array
     * @throws AuthSDKException
     */
    public function verifyAccessToken(string $token, string $signKeys): array
    {
        $parser = new Parser();
        try{
            $parsed = $parser->parse($token);
        }catch (\Exception $e){
            throw new AuthSDKException("the given token cannot be parsed");
        }
        $signer = new Sha256();
        return $this->verifyToken(
            $parsed,
            $signer,
            $signKeys
        );
    }

    /** Verify if the token is valid and returns the payload
     *
     * @param Token  $token
     * @param Signer $signer
     * @param string $signKey
     *
     * @return array
     * @throws AuthSDKException
     */
    private function verifyToken(Token $token, Signer $signer, string $signKey): array
    {
        $validation = new ValidationData();
        $validation->setIssuer('Authentication Server');
        $aud = $token->getClaim('aud');
        $validation->setCurrentTime(time());

        try{
            $verify = $token->verify(
                $signer,
                $signKey
            );
        }catch (\Exception $e){
            throw new AuthSDKException(
                "the accessToken can not be verified. This can be because of a wrong key or invalid token"
            );
        }
        if ($verify && $token->validate($validation) && in_array(
                $this->config->getOrigin(),
                $aud
            )
        ) {
            $claims = $token->getClaims();
            $results = [];
            foreach ($claims as $claim) {
                $results[$claim->getName()] = $claim->getValue();
            }
            return $results;
        }else {
            throw new AuthSDKException("the accessToken is not valid");
        }
    }
}