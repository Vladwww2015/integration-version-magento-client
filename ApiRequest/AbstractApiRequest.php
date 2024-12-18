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
     * @var string[]
     */
    protected static $unauthorizationMessageParts = [
        'Oops! Looks like you\'re not allowed to access this page. It seems you\'re missing the necessary credentials.',
        '401 Unauthorized'
    ];

    /**
     * @param GetCachedTokenInterface $getCachedToken
     * @param TokenParamInterfaceFactory $tokenParamFactory
     * @param ConfigProviderInterface $configProvider
     * @param string $type
     * @param string $name
     * @param string $tokenApiMethod
     * @param string $latestHashApiMethod
     * @param string $identitiesApiMethod
     * @param string $dataByIdentitiesMethod
     * @param string $deletedIdentitiesMethod
     */
    public function __construct(
        protected GetCachedTokenInterface $getCachedToken,
        protected TokenParamInterfaceFactory $tokenParamFactory,
        protected ConfigProviderInterface $configProvider,
        protected string $type,
        protected string $name,
        protected string $tokenApiMethod,
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
     * @throws ApiTokenNotDefined
     * @throws ApiUrlNotDefined
     */
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
        ],
               $attempts = 0
    ): array
    {
        $client = $this->initRequest($type, $headers);

        $fullApiUrl = $this->_getTrimmedUrl($this->configProvider->getApiUrl(), $apiUrlMethod);
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

        try {
            $response = $client->{$httpMethod}($fullApiUrl, $params);
            $content = $response->getBody()->getContents();

            return $content ? json_decode($content, true) : [];
        }  catch (\Throwable $e) {
            if($attempts++ <= 5) {
                foreach (static::$unauthorizationMessageParts as $part) {
                    $prevToken = $this->getCachedToken->getToken($this->_getTokenParam());
                    if(stristr($e->getMessage(), $part)) {
                        sleep(10);
                        $token = $this->getCachedToken->getToken($this->_getTokenParam());
                        if($prevToken === $token) $this->getCachedToken->resetToken($this->_getTokenParam());

                        return $this->_request($type, $params, $apiUrlMethod, $httpMethod, $headers, $attempts);
                    }
                }
            }
            throw $e;
        }
    }

    protected function _getTokenParam(): TokenParamInterface
    {
        $apiUrl = $this->configProvider->getApiUrl();
        $apiKey = $this->configProvider->getApiKey();
        $apiSecretKey = $this->configProvider->getApiSecretKey();
        return $this->tokenParamFactory->create([
            'apiUrl' => $apiUrl,
            'apiKey' => $apiKey,
            'apiSecretKey' => $apiSecretKey,
        ]);
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
        $apiUrl = $this->configProvider->getApiUrl();
        if($type !== 'token') {
            $apiKey = $this->configProvider->getApiKey();
            $apiSecretKey = $this->configProvider->getApiSecretKey();
            $tokenParam = $this->_getTokenParam();
            $token = $this->getCachedToken->getToken($tokenParam);
//            $token = $this->getCachedToken->getToken($tokenParam) ?: $this->configProvider->getApiToken();

            $this->token = $this->token ?: $token;
            if(!$this->token) {
                $this->token = $this->getToken($apiKey, $apiSecretKey);
                $this->getCachedToken->setToken($tokenParam, $this->token);
                if(!$this->token) {
                    throw new ApiTokenNotDefined();
                }
            }

            $headers['Authorization'] = 'Bearer ' . $this->token;
        }

        return new Client([
            'base_url' => $apiUrl,
            'headers' => $headers,
            'verify' => false //TODO TODO TODO add additional config
        ]);
    }

    /**
     * @param string $apiUrl
     * @param string $resourcePath
     * @return string
     */
    private function _getTrimmedUrl(string $apiUrl, string $resourcePath)
    {
        $apiBaseUrl = rtrim($apiUrl, '/');

        if($resourcePath) {
            $resourcePath = trim($resourcePath, '/');
        }

        return $apiBaseUrl . '/' . $resourcePath;
    }
}
