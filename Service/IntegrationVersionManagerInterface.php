<?php

namespace IntegrationHelper\IntegrationVersionMagentoClient\Service;

use IntegrationHelper\IntegrationVersionMagentoClient\ApiRequest\LatestHashDataOutput;

interface IntegrationVersionManagerInterface
{
    public function getIdentities(string $source, string $currentHash, string $dateTime): iterable;

    public function getLatestHashData(string $source): LatestHashDataOutput;

    public function saveLatestHash(string $source, LatestHashDataOutput $latestHashDataOutput): void;
}
