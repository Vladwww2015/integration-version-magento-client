<?php

namespace IntegrationHelper\IntegrationVersionMagentoClient\ApiRequest;

use GuzzleHttp\Client;
use IntegrationHelper\IntegrationVersionMagentoClient\Exceptions\ApiTokenNotDefined;
use IntegrationHelper\IntegrationVersionMagentoClient\Exceptions\ApiUrlNotDefined;
use IntegrationHelper\IntegrationVersionMagentoClient\Model\ConfigProviderInterface;

abstract class AbstractApiRequest implements ApiRequestInterface
{

    protected string $token = '';

    /**
     * @var null
     */
    /**
     * @param ConfigProviderInterface $configProvider
     * @param string $type
     * @param string $name
     * @param string $tokenApiMethod
     * @param string $checkTokenApiMethod
     * @param string $latestHashApiMethod
     * @param string $identitiesApiMethod
     */
    public function __construct(
        protected ConfigProviderInterface $configProvider,
        protected string $type,
        protected string $name,
        protected string $tokenApiMethod,
        protected string $checkTokenApiMethod,
        protected string $latestHashApiMethod,
        protected string $identitiesApiMethod,
        protected string $dataByIdentitiesMethod,
        protected string $deletedIdentitiesMethod
    ){}

    /**
     * @param string $source
     * @param string $currentHash
     * @param string $hashDateTime
     * @return iterable
     * @throws ApiTokenNotDefined
     * @throws ApiUrlNotDefined
     */
    public function getIdentities(string $source, string $currentHash, string $hashDateTime): iterable
    {
        $page = 1;
        while(true) {
            $data = $this->_request(
                'identities',
                [
                    'source' => $source,
                    'old_hash' => $currentHash ?: 'empty',
                    'hash_date_time' => $hashDateTime,
                    'page' => $page++,
                    'limit' => 10000
                ],
                $this->identitiesApiMethod
            );

            if(!($data['identities'] ?? false)) break;
            yield $data;
        }
    }

    /**
     * @param string $source
     * @param array $identitiesForCheck
     * @return array
     * @throws ApiTokenNotDefined
     * @throws ApiUrlNotDefined
     */
    public function getDeletedIdentities(string $source, array $identitiesForCheck): array
    {
        $data = $this->_request(
            'deleted_identities',
            [
                'source' => $source,
                'identities_for_check' => $identitiesForCheck
            ],
            $this->deletedIdentitiesMethod
        );

        return $data['identities_for_delete'] ?? [];
    }

    /**
     * @param string $source
     * @param array $identities
     * @param int $limit
     * @return iterable
     */
    public function getDataByIdentities(string $source, array $identities, int $limit = 5000): iterable
    {
        return $this->_request(
            'dataByIdentities',
            [
                'source' => $source,
                'identities' => $identities
            ],
            $this->dataByIdentitiesMethod
        );
    }


    /**
     * @param string $source
     * @return array
     * @throws ApiTokenNotDefined
     * @throws ApiUrlNotDefined
     */
    public function getLatestHashData(string $source): LatestHashDataOutput
    {
        $data = $this->_request(
            'latest_hash',
            [
                'source' => $source,
            ],
            $this->latestHashApiMethod
        );

        return new LatestHashDataOutput(
            $data['hash'] ?? '',
            $data['hash_date_time'] ?? '',
            $data['message'] ?? '',
            $data['is_error'] ?? false
        );
    }

    /**
     * @param string $apiKey
     * @param string $apiSecretKey
     * @return string
     */
    protected function _checkToken(string $apiKey, string $apiSecretKey, string $token): bool
    {
        $data = $this->_request(
            'check_token',
            [
                'email' => $apiKey,
                'password' => $apiSecretKey,
                'device_name' => 'PC'
            ],
            $this->getCheckTokenApiMethod(),
            'POST',
            [
                'Accept' => 'application/json',
                'Content-Type' => 'multipart/form-data'
            ]
        );

        return is_array($data) ? (!!($data['id'] ?? false)) : false;
    }

    public function getToken(string $apiKey = '', string $apiSecretKey = ''): string
    {
        $data = $this->_request(
            'token',
            [
                'email' => $apiKey,
                'password' => $apiSecretKey,
                'device_name' => 'PC'
            ],
            $this->getTokenApiMethod(),
            'POST',
            [
                'Accept' => 'application/json',
                'Content-Type' => 'multipart/form-data'
            ]
        );

        return $data['token'] ?? '';
    }

    /**
     * @return string
     */
     public function getType(): string
     {
         return $this->type;
     }

    /**
     * @return string
     */
     public function getName(): string
     {
         return $this->name;
     }

    /**
     * @param string $type
     * @param array $headers
     * @return Client
     * @throws ApiTokenNotDefined
     * @throws ApiUrlNotDefined
     */
     protected function initRequest(
         string $type,
         array $headers = [
             'Accept' => 'application/json',
             'Content-Type' => 'application/json'
         ]
     ): Client
     {
         $this->token = $this->token ?: $this->configProvider->getApiToken();

         if($type !== 'token') {
             $hasToken = false;
             if($this->token) {
                 $hasToken = $this->_checkToken($this->configProvider->getApiKey(), $this->configProvider->getApiSecretKey(), $this->token);
             }
             if(!$hasToken) {
                 $this->token = $this->getToken($this->configProvider->getApiKey(), $this->configProvider->getApiSecretKey());
                 if(!$this->token) {
                     throw new ApiTokenNotDefined();
                 }
             }

             $headers['Authorization'] = 'Bearer ' . $this->token;
         }

         return new Client([
             'base_url' => $this->configProvider->getApiUrl(),
             'headers' => $headers,
             'verify' => false //TODO TODO TODO add additional config
         ]);
     }

    /**
     * @return string
     */
    public function getCheckTokenApiMethod(): string
    {
        return $this->checkTokenApiMethod;
    }

    public function getTokenApiMethod(): string
    {
        return $this->tokenApiMethod;
    }

    /**
     * @return string
     */
    public function getLatestHashApiMethod(): string
    {
        return $this->latestHashApiMethod;
    }

    /**
     * @return string
     */
    public function getIdentitiesApiMethod(): string
    {
        return $this->identitiesApiMethod;
    }

    /**
     * @param string $type
     * @param array $params
     * @param string $method
     * @param array $headers
     * @return array
     * @throws ApiTokenNotDefined
     * @throws ApiUrlNotDefined
     */
     protected function _request(
         string $type,
         array $params,
         string $apiUrlMethod,
         string $httpMethod = 'POST',
         array $headers = [
             'Accept' => 'application/json',
             'Content-Type' => 'application/json'
         ]
     ): array
     {
        $client = $this->initRequest($type, $headers);

         $fullApiUrl = $this->getTrimmedUrl($this->configProvider->getApiUrl(), $apiUrlMethod);
         $httpMethod = strtoupper($httpMethod);
         $params = match($httpMethod) {
             'POST' => [
                 'json' => $params
             ],
             'PUT' => [
                 'json' => [
                     'data' => $params
                 ]
             ],
             default => $params
         };
         $response = $client->{$httpMethod}($fullApiUrl, $params);
         $content = $response->getBody()->getContents();
         return $content ? json_decode($content, true) : [];
     }

    /**
     * @param string $apiUrl
     * @param string $resourcePath
     * @return string
     */
    private function getTrimmedUrl(string $apiUrl, string $resourcePath)
    {
        $apiBaseUrl = rtrim($apiUrl, '/');

        if($resourcePath) {
            $resourcePath = trim($resourcePath, '/');
        }

        return $apiBaseUrl . '/' . $resourcePath;
    }
}
