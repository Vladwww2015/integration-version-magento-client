<?php

namespace IntegrationHelper\IntegrationVersionMagentoClient\Model;

use IntegrationHelper\IntegrationVersionMagentoClient\Api\DeleteOldDataRequestInterface;

class DeleteOldDataRequest implements DeleteOldDataRequestInterface
{
    /**
     * @var string
     */
    protected string $source = '';

    /**
     * @return string
     */
    public function getSource(): string
    {
        return $this->source;
    }

    /**
     * @param string $source
     * @return DeleteOldDataRequestInterface
     */
    public function setSource(string $source): DeleteOldDataRequestInterface
    {
        $this->source = $source;

        return $this;
    }
}

