<?php
namespace IntegrationHelper\IntegrationVersionMagentoClient\Exceptions;

class ApiRequestAlreadyExists extends \Exception
{
    public function __construct(string $type)
    {
        parent::__construct(__('Integration Version Api Request with Type %1 already exists. Please change Type for your custom Api Request', $type));
    }
}
