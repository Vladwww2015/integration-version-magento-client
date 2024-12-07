<?php

namespace IntegrationHelper\IntegrationVersionMagentoClient\Service;

use IntegrationHelper\IntegrationVersionMagentoClient\ApiRequest\LatestHashDataOutput;

interface IntegrationVersionManagerInterface
{
    public function getIdentities(string $source, string $currentHash, string $dateTime): iterable;

    /**
     * @param string $source
     * @param array $identities
     * @param int $limit
     * @return iterable
     */
    public function getDataByIdentities(string $source, array $identities, int $limit = 5000): iterable;


    public function getLatestHashData(string $source): LatestHashDataOutput;

    public function saveLatestHash(string $source, LatestHashDataOutput $latestHashDataOutput): void;
}
