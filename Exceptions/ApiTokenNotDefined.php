<?php
namespace IntegrationHelper\IntegrationVersionMagentoClient\Exceptions;

class ApiTokenNotDefined extends \Exception
{
    public function __construct()
    {
        parent::__construct(__('Api Token has not been received or defined'));
    }
}
