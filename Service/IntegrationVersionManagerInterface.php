<?php

namespace IntegrationHelper\IntegrationVersionMagentoClient\Service;

use IntegrationHelper\IntegrationVersionMagentoClient\ApiRequest\LatestHashDataOutput;

interface IntegrationVersionManagerInterface
{
    /**
     * @param string $source
     * @param string $currentHash
     * @param string $dateTime
     * @param int|null $pageFrom
     * @param int|null $pageTo
     * @return iterable
     */
    public function getIdentities(
        string $source,
        string $currentHash,
        string $dateTime,
        int $pageFrom = null,
        int $pageTo = null
    ): iterable;

    /**
     * @param string $source
     * @param string $currentHash
     * @param string $dateTime
     * @return array
     */
    public function getIdentitiesTotal(
        string $source,
        string $currentHash,
        string $dateTime
    ): array;

    /**
     * @param string $source
     * @param array $identities
     * @param int $limit
     * @return iterable
     */
    public function getDataByIdentities(string $source, array $identities, int $limit = 5000): iterable;

    /**
     * @param string $source
     * @param array $identitiesForCheck
     * @return array
     */
    public function getDeletedIdentities(string $source, array $identitiesForCheck): array;


    public function getLatestHashData(string $source): LatestHashDataOutput;

    public function saveLatestHash(string $source, LatestHashDataOutput $latestHashDataOutput): void;
}
