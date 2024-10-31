<?php
namespace IntegrationHelper\IntegrationVersionMagentoClient\Model;

interface ConfigProviderInterface
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
    public function getApiToken(): string;
}
