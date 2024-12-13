<?php

namespace IntegrationHelper\IntegrationVersionMagentoClient\Queue;

use IntegrationHelper\IntegrationVersion\IntegrationVersionManagerInterface;
use IntegrationHelper\IntegrationVersion\Repository\IntegrationVersionRepositoryInterface;
use IntegrationHelper\IntegrationVersionMagentoClient\Api\DeleteOldDataRequestInterface;
use Magento\Framework\App\ResourceConnection;

class CleanOldDataConsumer
{
    public function __construct(
        protected ResourceConnection $resourceConnection,
        protected IntegrationVersionManagerInterface $integrationVersionManager,
        protected IntegrationVersionRepositoryInterface $integrationVersionRepository
    ){}

    public function process(DeleteOldDataRequestInterface $request)
    {
        $integrationVersion = $this->integrationVersionRepository->getItemBySource($request->getSource());

        $tableName = $integrationVersion->getTableName();
        $connection = $this->resourceConnection->getConnection();
        $identityColumn = $integrationVersion->getIdentityColumn();
        $page = 1;
        while(true) {
            $query = $connection->select()
                ->from($tableName, $identityColumn)
                ->where($identityColumn . ' IS NOT NULL')
                ->order($identityColumn . ' ASC')
                ->limitPage($page++, 50000);
            $identities = $connection->fetchCol($query);
            if(!$identities) break;
            $deletedIdentities = $this->integrationVersionManager
                ->getDeletedIdentities($request->getSource(), $identities);

            if($deletedIdentities) {
                $deleteQuery = $connection->deleteFromSelect(
                    $connection->select()->from($tableName)->where($identityColumn . ' IN (?)', $deletedIdentities),
                    $tableName
                );
                $connection->query($deleteQuery);
            }
        }
    }
}
