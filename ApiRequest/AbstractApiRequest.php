<?php

namespace IntegrationHelper\IntegrationVersionMagentoClient\ApiRequest;

use GuzzleHttp\Client;
use IntegrationHelper\IntegrationVersionMagentoClient\Exceptions\ApiTokenNotDefined;
use IntegrationHelper\IntegrationVersionMagentoClient\Exceptions\ApiUrlNotDefined;
use IntegrationHelper\IntegrationVersionMagentoClient\Model\ConfigProviderInterface;

abstract class AbstractApiRequest implements ApiRequestInterface
{
    /**
     * @var
     */
    private $clients = [];

    /**
     * @var null
     */
    protected $token = null;

    /**
     * @param ConfigProviderInterface $configProvider
     * @param string $type
     * @param string $name
     * @param string $tokenApiMethod
     * @param string $latestHashApiMethod
     * @param string $identitiesApiMethod
     */
    public function __construct(
        protected ConfigProviderInterface $configProvider,
        protected string $type,
        protected string $name,
        protected string $tokenApiMethod,
        protected string $latestHashApiMethod,
        protected string $identitiesApiMethod
    ){}

    /**
     * @param string $source
     * @param string $currentHash
     * @param string $dateTime
     * @return iterable
     * @throws ApiTokenNotDefined
     * @throws ApiUrlNotDefined
     */
    public function getIdentities(string $source, string $currentHash, string $dateTime): iterable
    {
        $page = 1;
        while(true) {
            $data = $this->_request(
                'identities',
                [
                    'source' => $source,
                    'old_hash' => $currentHash,
                    'updated_at' => $dateTime,
                    'page' => $page++,
                    'limit' => 10000
                ],
                $this->identitiesApiMethod
            );

            yield $data;
            break;
        }
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
            $data['updated_at'] ?? '',
            $data['message'] ?? '',
            $data['is_error'] ?? false
        );
    }

    /**
     * @param string $apiKey
     * @param string $apiSecretKey
     * @return string
     */
    public function getToken(string $apiKey = '', string $apiSecretKey = ''): string
    {
        if($this->token === null) {
            $data = $this->_request(
                'token',
                [
                    'json' => [
                        'email' => $apiKey,
                        'password' => $apiSecretKey,
                        'device_name' => 'PC'
                    ]
                ],
                $this->tokenApiMethod
            );

            $this->token = $data['token'] ?? '';
        }

        return $this->token;
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
        $client = $this->clients[$type] ?? false;
         if($client === false) {
             $token = $this->configProvider->getApiToken();
             $apiUrl = $this->configProvider->getApiUrl();
             if(!$apiUrl) {
                 throw new ApiUrlNotDefined();
             }
             if(!$token) {
                 $token = $this->getToken($this->configProvider->getApiKey(), $this->configProvider->getApiSecretKey());
                 if(!$token) {
                     throw new ApiTokenNotDefined();
                 }
             }

             $headers['Authorization'] = 'Bearer ' . $token;

             $this->clients[$type] = new Client([
                 'base_url' => $apiUrl,
                 'headers' => $headers,
                 'verify' => false //TODO TODO TODO add additional config
             ]);
         }

         return $client;
     }

    /**
     * @return string
     */
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
        $response = $client->{strtoupper($httpMethod)}($fullApiUrl, $params);

        return $response->getBody()->getContents();
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
