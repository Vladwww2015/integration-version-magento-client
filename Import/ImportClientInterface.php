<?php
namespace IntegrationHelper\IntegrationVersionMagentoClient\Import;

use IntegrationHelper\IntegrationVersion\Repository\IntegrationVersionRepositoryInterface;
use IntegrationHelper\IntegrationVersionMagentoClient\Service\IntegrationVersionManagerInterface;

interface ImportClientInterface
{
    /**
     * @return string
     */
    public function getSourceCode(): string;

    /**
     * @return iterable
     */
    public function itemsData(): iterable;

    public function callbackAfterClearOldData(): void;

    public function callbackBeforeSaveLatestHash(): void;

    public function callbackAfterReturnData(): void;

    public function callbackBeforeGetItem(): void;

    public function callbackBeforeStart(): void;
}
