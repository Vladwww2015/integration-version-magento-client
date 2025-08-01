<?php

namespace IntegrationHelper\IntegrationVersionMagentoClient\Import;

use IntegrationHelper\IntegrationVersion\Repository\IntegrationVersionRepositoryInterface;
use IntegrationHelper\IntegrationVersionMagentoClient\Api\DeleteOldDataRequestInterface;
use IntegrationHelper\IntegrationVersionMagentoClient\Service\IntegrationVersionManagerInterface;
use Magento\Framework\MessageQueue\PublisherInterface;

abstract class AbstractImportClient implements ImportClientInterface
{
    /**
     * @param IntegrationVersionRepositoryInterface $integrationVersionRepository
     * @param IntegrationVersionManagerInterface $integrationVersionManager
     * @param PublisherInterface $publisher
     * @param DeleteOldDataRequestInterface $requestData
     * @param string $queueClearOldDataKey
     */
    public function __construct(
        protected IntegrationVersionRepositoryInterface $integrationVersionRepository,
        protected IntegrationVersionManagerInterface $integrationVersionManager,
        protected PublisherInterface $publisher,
        protected DeleteOldDataRequestInterface $requestData,
        protected string $queueClearOldDataKey = 'import.clean.old.data',
    ){}

    /**
     * @return iterable
     * @throws \Exception
     */
    public function itemsData(): iterable
    {
        $integrationVersion = $this->integrationVersionRepository->getItemBySource($this->getSourceCode());
        if($integrationVersion && $integrationVersion->getSource()) {
            $latestHashOutput = $this->integrationVersionManager->getLatestHashData($this->getSourceCode());
            if($integrationVersion->getHash() !== $latestHashOutput->getHash()) {
                $dataGenerator = $this->integrationVersionManager->getIdentities($this->getSourceCode(), $integrationVersion->getHash(), $integrationVersion->getHashDateTime());

                $this->callbackBeforeStart();

                $break = false;
                foreach ($dataGenerator as $identityData) {
                    if(!array_key_exists('identities', $identityData) || !count($identityData['identities'])) break;

                    $this->callbackBeforeGetItem();

                    foreach (array_chunk($identityData['identities'], 10000) as $chunk) {
                        $itemsData = $this->integrationVersionManager->getDataByIdentities(
                            $this->getSourceCode(),
                            array_column($chunk, 'identity_value')
                        );

                        $isError = $itemsData['is_error'] ?? false;
                        if($isError) throw new \Exception($itemsData['message']);

                        if(!array_key_exists('data', $itemsData) || !count($itemsData['data'])) continue;

                        yield $itemsData['data'] ?? [];
                    }

                    $this->callbackAfterReturnData();
                }
                $this->callbackBeforeSaveLatestHash();

                $this->integrationVersionManager->saveLatestHash($this->getSourceCode(), $latestHashOutput);
                $this->_clearOldData();

                $this->callbackAfterClearOldData();
            }
        }
    }

    /**
     * @return void
     */
    protected function _clearOldData(): void
    {
        $this->requestData->setSource($this->getSourceCode());

        $this->publisher->publish($this->queueClearOldDataKey, $this->requestData);
    }
}
