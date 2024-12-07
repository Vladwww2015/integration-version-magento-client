<?php

namespace IntegrationHelper\IntegrationVersionMagentoClient\Service;

use IntegrationHelper\BaseLogger\Logger\Logger;
use IntegrationHelper\IntegrationVersionMagentoClient\ApiRequest\ApiRequestInterface;
use IntegrationHelper\IntegrationVersionMagentoClient\ApiRequest\LatestHashDataOutput;
use IntegrationHelper\IntegrationVersion\Repository\IntegrationVersionRepositoryInterface;

class IntegrationVersionManager implements IntegrationVersionManagerInterface
{
    /**
     * @param ApiRequestInterface $apiRequest
     * @param IntegrationVersionRepositoryInterface $integrationVersionRepository
     */
    public function __construct(
        protected ApiRequestInterface $apiRequest,
        protected IntegrationVersionRepositoryInterface $integrationVersionRepository
    ) {}

    /**
     * @param string $source
     * @param string $currentHash
     * @param string $dateTime
     * @return iterable
     */
    public function getIdentities(string $source, string $currentHash, string $dateTime): iterable
    {
        return $this->apiRequest->getIdentities($source, $currentHash, $dateTime);
    }

    /**
     * @param string $source
     * @return LatestHashDataOutput
     */
    public function getLatestHashData(string $source): LatestHashDataOutput
    {
        return $this->apiRequest->getLatestHashData($source);
    }

    public function getDataByIdentities(string $source, array $identities, int $limit = 5000): iterable
    {
        return $this->apiRequest->getDataByIdentities($source, $identities, $limit);
    }

    /**
     * @param string $source
     * @param LatestHashDataOutput $latestHashDataOutput
     * @return void
     */
    public function saveLatestHash(string $source, LatestHashDataOutput $latestHashDataOutput): void
    {
        if($latestHashDataOutput->isError()) {
            Logger::log($latestHashDataOutput->getMessage(), 'integration_version_magento_crit');
            return;
        }

        $item = $this->integrationVersionRepository->getItemBySource($source);
        $item->setStatus(\IntegrationHelper\IntegrationVersion\Model\IntegrationVersionInterface::STATUS_READY);
        $item->setHash($latestHashDataOutput->getHash(), $latestHashDataOutput->getHashDateTime());
        $item->setUpdatedAtValue($latestHashDataOutput->getHashDateTime());
        $this->integrationVersionRepository->updateItem($item);
    }
}
