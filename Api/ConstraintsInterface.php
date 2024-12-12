<?php
namespace IntegrationHelper\IntegrationVersionMagentoClient\Api;

interface ConstraintsInterface
{
    public const XML_PATH_IS_ENABLED = 'integration_version/settings/credentials/is_enabled';
    public const XML_PATH_API_URL = 'integration_version/settings/credentials/api_url';
    public const XML_PATH_API_KEY = 'integration_version/settings/credentials/api_key';
    public const XML_PATH_API_SECRET_KEY = 'integration_version/settings/credentials/api_secret_key';
    public const XML_PATH_API_TOKEN = 'integration_version/settings/credentials/api_token';

    /**
     * @deprecated
     */
    public const BASE_TOKEN_METHOD = 'api/v1/admin/login';

    public const LOGIN_AND_TOKEN_METHOD = 'api/v1/admin/check-login-and-get-token';

    public const BASE_GET_IDENTITIES_METHOD = 'api/v1/admin/integration-version/get-identities';
    public const BASE_GET_LATEST_HASH_METHOD = 'api/v1/admin/integration-version/get-latest-hash';

    public const BASE_GET_DATA_BY_IDENTITIES_METHOD = 'api/v1/admin/integration-version/get-data-by-identities';

    public const BASE_GET_DELETED_IDENTITIES_METHOD = 'api/v1/admin/integration-version/get-deleted-identities';
}
