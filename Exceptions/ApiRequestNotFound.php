<?php
namespace IntegrationHelper\IntegrationVersionMagentoClient\Exceptions;

class ApiRequestNotFound extends \Exception
{
    public function __construct(string $type)
    {
        parent::__construct(__('Integration Version Api Request with Type %1 not found', $type));
    }
}
