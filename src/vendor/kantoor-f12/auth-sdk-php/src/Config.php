<?php

namespace AuthSDK;

use GuzzleHttp\Client;

class Config
{
    /**
     * @var string
     */
    private $origin;

    /**
     * @var string
     */
    private $authServerTokenPrivateKey;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var string
     */
    private $accessTokenPublicKey;

    /**
     * @return string
     */
    public function getAccessTokenPublicKey()
    {
        return $this->accessTokenPublicKey;
    }

    /**
     * @var Client
     */
    protected $client;

    /** get the guzzle client from the config
     *
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /** set the guzzle client
     *
     * @param Client $client
     */
    public function setClient(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $accessTokenPublicKey
     */
    public function setAccessTokenPublicKey($accessTokenPublicKey)
    {
        $this->accessTokenPublicKey = $accessTokenPublicKey;
    }

    /** set the origin
     *
     * @param string $origin
     */
    public function setOrigin(string $origin)
    {
        $this->origin = $origin;
    }

    /** set the authServerTokenPrivateKey
     *
     * @param string $authServerTokenPrivateKey
     */
    public function setAuthServerTokenPrivateKey(string $authServerTokenPrivateKey)
    {
        $this->authServerTokenPrivateKey = $authServerTokenPrivateKey;
    }

    /** set the base url of the auth server
     *
     * @param string $baseUrl
     */
    public function setBaseUrl(string $baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    /** get the origin from the config
     *
     * @return string
     */
    public function getOrigin(): string
    {
        return $this->origin;
    }

    /**get the authServerTokenPrivateKey from the config
     *
     * @return string
     */
    public function getAuthServerTokenPrivateKey(): string
    {
        return $this->authServerTokenPrivateKey;
    }

    /**get the base url from the config
     *
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }
}