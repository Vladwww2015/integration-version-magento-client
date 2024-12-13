<?php

namespace IntegrationHelper\IntegrationVersionMagentoClient\ApiRequest;

interface GetCachedTokenInterface
{
    /**
     * @param TokenParamInterface $tokenParam
     * @return string|bool
     */
    public function getToken(TokenParamInterface $tokenParam): string|bool;

    /**
     * @param TokenParamInterface $tokenParam
     * @param string $newToken
     * @return string
     */
    public function setToken(TokenParamInterface $tokenParam, string $newToken): GetCachedTokenInterface;

    /**
     * @param TokenParamInterface $tokenParam
     * @return GetCachedTokenInterface
     */
    public function resetToken(TokenParamInterface $tokenParam): GetCachedTokenInterface;
}
