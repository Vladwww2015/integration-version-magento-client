<?php

namespace IntegrationHelper\IntegrationVersionMagentoClient\Api;

/**
 *
 */
interface DeleteOldDataRequestInterface
{
    /**
     * @return string
     */
    public function getSource(): string;

    /**
     * @param string $source
     * @return \IntegrationHelper\IntegrationVersionMagentoClient\Api\DeleteOldDataRequestInterface
     */
    public function setSource(string $source): DeleteOldDataRequestInterface;
}
