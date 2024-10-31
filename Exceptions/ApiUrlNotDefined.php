<?php
namespace IntegrationHelper\IntegrationVersionMagentoClient\Exceptions;

class ApiUrlNotDefined extends \Exception
{
    public function __construct()
    {
        parent::__construct(__('Api Url has not been defined'));
    }
}
