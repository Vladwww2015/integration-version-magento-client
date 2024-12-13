<?php

namespace IntegrationHelper\IntegrationVersionMagentoClient\ApiRequest;

interface TokenParamInterface
{
    /**
     * @return string
     */
    public function getApiUrl(): string;

    /**
     * @return string
     */
    public function getApiKey(): string;

    /**
     * @return string
     */
    public function getApiSecretKey(): string;

    /**
     * @return string
     */
    public function getTokenKey(): string;
}
