# Magento 2 Extension.

## Usage
### Default: 
#### Add Configuration in System Admin Config (Api Url, Api Key, Api Secret Key or Token)
```php
use IntegrationHelper\IntegrationVersion\Repository\IntegrationVersionRepositoryInterface;

public function __construct(
        protected IntegrationVersionRepositoryInterface $integrationVersionRepository,
        protected IntegrationVersionManager $integrationVersionManager
    ){}

 public function importsRun() {
 foreach ($this->integrationVersionRepository->getItems() as $item) {
            $source = $item->getSource();
            $latestOutputData = $this->integrationVersionManager->getLatestHashData($source);
            if($latestOutputData->isError()) {
                $this->integrationVersionManager->saveLatestHash($source, $latestOutputData);
                continue;
            }

            $identities = [];
            foreach (
                $this->integrationVersionManager
                    ->getIdentities(
                        $source,
                        $item->getHash(),
                        $item->getUpdatedAtValue())
                as $items) {
                $identities[] = //.... get identities 
            }
            
            //Get Yours Api request from Mapped $source-$request
            
            if($identities) {
                $request->importByIdentities($identities);
                //Request by Api and get Items by ids
            }
        }
 }
  

```

### Custom:
#### Add Configuration in System Admin Config (Api Url, Api Key, Api Secret Key or Token)


```xml

<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="urn:magento:framework:ObjectManager/etc/config.xsd">

    <virtualType name="Vendor\ApiAppIntegrationVersion\ApiRequest\EngineApiRequest"
                 type="IntegrationHelper\IntegrationVersionMagentoClient\ApiRequest\BaseApiRequest">
        <arguments>
            <argument name="configProvider" xsi:type="object">Vendor\ApiAppIntegrationVersion\Model\ConfigProvider</argument>
            <argument name="tokenApiMethod" xsi:type="const">\IntegrationHelper\IntegrationVersionMagentoClient\Api\ConstraintsInterface::BASE_TOKEN_METHOD</argument>
            <argument name="identitiesApiMethod" xsi:type="const">\IntegrationHelper\IntegrationVersionMagentoClient\Api\ConstraintsInterface::BASE_GET_IDENTITIES_METHOD</argument>
            <argument name="latestHashApiMethod" xsi:type="const">\IntegrationHelper\IntegrationVersionMagentoClient\Api\ConstraintsInterface::BASE_GET_LATEST_HASH_METHOD</argument>
            <argument name="type" xsi:type="string">engine_api_request</argument>
            <argument name="name" xsi:type="string">Engine Api Request</argument>
        </arguments>
    </virtualType>

    <virtualType
        name="Vendor\ApiAppIntegrationVersion\Service\EngineIntegrationVersionManager"
        type="IntegrationHelper\IntegrationVersionMagentoClient\Service\IntegrationVersionManager"
    >
        <arguments>
            <argument name="apiRequest" xsi:type="object">Vendor\ApiAppIntegrationVersion\ApiRequest\EngineApiRequest</argument>
        </arguments>
    </virtualType>

    <type name="Vendor\ApiAppIntegrationVersion\Service\RunImports">
        <arguments>
            <argument name="integrationVersionManager" xsi:type="object">Vendor\ApiAppIntegrationVersion\Service\EngineIntegrationVersionManager</argument>
        </arguments>
    </type>
</config>


```

```php 
Vendor\ApiAppIntegrationVersion\Service\RunImports:

<?php

declare(strict_types=1);

namespace Vendor\ApiAppIntegrationVersion\Service;

use IntegrationHelper\IntegrationVersion\Repository\IntegrationVersionRepositoryInterface;
use IntegrationHelper\IntegrationVersionMagentoClient\Service\IntegrationVersionManager;
use IntegrationHelper\IntegrationVersion\Model\IntegrationVersionInterface;

class RunImports
{

    public function __construct(
        protected IntegrationVersionRepositoryInterface $integrationVersionRepository,
        protected IntegrationVersionManager $integrationVersionManager
    ){}

    /**
     * @param array $args
     * @return void
     */
    public function execute()
    {
        /**
         * @var $item IntegrationVersionInterface
         */
        foreach ($this->integrationVersionRepository->getItems() as $item) {
            $source = $item->getSource();
            $latestOutputData = $this->integrationVersionManager->getLatestHashData($source);
            if($latestOutputData->isError()) {
                $this->integrationVersionManager->saveLatestHash($source, $latestOutputData);
                continue;
            }

            $identities = [];
            foreach (
                $this->integrationVersionManager
                    ->getIdentities(
                        $source,
                        $item->getHash(),
                        $item->getUpdatedAtValue())
                as $items) {
               // $identities[] = //.... get identities 
            }
            
            if($identities) {
                //Request by Api and get Items by ids
            }
        }

    }
}

```
