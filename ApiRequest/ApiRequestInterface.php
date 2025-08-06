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
    public function getIdentities(string $source, string $currentHash, string $hashDateTime): iterable;

    /**
     * @param int $pageFrom
     * @param int $pageTo
     * @return void
     */
    public function setIdentitiesRangePage(int $pageFrom, int $pageTo): void;

    /**
     * @return void
     */
    public function resetIdentitiesRangePage(): void;

    /**
     * @param string $source
     * @param array $identities
     * @param int $limit
     * @return iterable
     */
    public function getDataByIdentities(string $source, array $identities, int $limit = 5000): iterable;

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

    /**
     * @param string $source
     * @param array $identitiesForCheck
     * @return array
     */
    public function getDeletedIdentities(string $source, array $identitiesForCheck): array;
}
