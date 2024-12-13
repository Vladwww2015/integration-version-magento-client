<?php

namespace IntegrationHelper\IntegrationVersionMagentoClient\ApiRequest;

use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\Cache\TypeListInterface;

class GetCachedToken implements GetCachedTokenInterface
{
    public const CACHE_LIFETIME = 72000;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param CacheInterface $cache
     * @param TypeListInterface $cacheTypeList
     */
    public function __construct(
        protected \Magento\Framework\App\Helper\Context $context,
        protected CacheInterface $cache,
        protected TypeListInterface $cacheTypeList
    ){}

    /**
     * @param TokenParamInterface $tokenParam
     * @return string|bool
     */
    public function getToken(TokenParamInterface $tokenParam): string|bool
    {
        $cachedValue = $this->cache->load($tokenParam->getTokenKey());

        return $cachedValue ?: false;
    }

    public function resetToken(TokenParamInterface $tokenParam): GetCachedTokenInterface
    {
        $this->cache->remove($tokenParam->getTokenKey());

        return $this;
    }

    /**
     * @param TokenParamInterface $tokenParam
     * @param string $newToken
     * @return GetCachedTokenInterface
     */
    public function setToken(TokenParamInterface $tokenParam, string $newToken): GetCachedTokenInterface
    {
        $this->cache->save(
            $newToken,
            $tokenParam->getTokenKey(),
            [],
            static::CACHE_LIFETIME
        );

        return $this;
    }
}
