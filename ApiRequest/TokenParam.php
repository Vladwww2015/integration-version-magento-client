<?php

namespace IntegrationHelper\IntegrationVersionMagentoClient\ApiRequest;

class TokenParam implements TokenParamInterface
{
    public function __construct(
        protected string $apiUrl,
        protected string $apiKey,
        protected string $apiSecretKey
    ){}

    /**
     * @return string
     */
    public function getApiUrl(): string
    {
        return $this->apiUrl;
    }

    /**
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * @return string
     */
    public function getApiSecretKey(): string
    {
        return $this->apiSecretKey;
    }

    /**
     * @return string
     */
    public function getTokenKey(): string
    {
        return md5($this->getApiUrl() . ':' . $this->getApiKey() . ':' . $this->getApiSecretKey());
    }
}
