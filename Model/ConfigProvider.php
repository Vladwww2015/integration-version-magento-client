<?php
namespace IntegrationHelper\IntegrationVersionMagentoClient\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use IntegrationHelper\IntegrationVersionMagentoClient\Api\ConstraintsInterface;

class ConfigProvider implements ConfigProviderInterface
{
    /**
     * @param ScopeConfigInterface $_scopeConfig
     */
    public function __construct(protected ScopeConfigInterface $_scopeConfig)
    {}

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->_scopeConfig->isSetFlag(ConstraintsInterface::XML_PATH_IS_ENABLED);
    }

    /**
     * @return string
     */
    public function getApiUrl(): string
    {
        return $this->_scopeConfig->getValue(ConstraintsInterface::XML_PATH_API_URL);
    }

    /**
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->_scopeConfig->getValue(ConstraintsInterface::XML_PATH_API_KEY);
    }

    /**
     * @return string
     */
    public function getApiSecretKey(): string
    {
        return $this->_scopeConfig->getValue(ConstraintsInterface::XML_PATH_API_SECRET_KEY);
    }

    /**
     * @return string
     */
    public function getApiToken(): string
    {
        return $this->_scopeConfig->getValue(ConstraintsInterface::XML_PATH_API_TOKEN);
    }
}
