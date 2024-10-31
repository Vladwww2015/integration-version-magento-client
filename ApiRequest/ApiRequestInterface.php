<?php

namespace IntegrationHelper\IntegrationVersionMagentoClient\ApiRequest;

interface ApiRequestInterface
{
    /**
     * Unique Type
     *
     * @return string
     */
    public function getType(): string;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return iterable
     */
    public function getIdentities(string $source, string $currentHash, string $dateTime): iterable;

    /**
     * @param string $source
     * @return LatestHashDataOutput
     */
    public function getLatestHashData(string $source): LatestHashDataOutput;

    /**
     * @return string
     */
    public function getTokenApiMethod(): string;

    /**
     * @param string $apiKey
     * @param string $apiSecretKey
     * @return string
     */
    public function getToken(string $apiKey = '', string $apiSecretKey = ''): string;

    /**
     * @return string
     */
    public function getLatestHashApiMethod(): string;

    /**
     * @return string
     */
    public function getIdentitiesApiMethod(): string;
}
