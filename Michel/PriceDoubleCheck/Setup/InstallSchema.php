<?php

namespace Michel\PriceDoubleCheck\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Michel\PriceDoubleCheck\Api\Data\PriceApproveInterface;
use Michel\PriceDoubleCheck\Model\ResourceModel\PriceApprove as PriceApproveResourceModel;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $this->createPriceApproveTable($setup);
        $setup->endSetup();
    }

    public function createPriceApproveTable(SchemaSetupInterface $installer)
    {

        $table = $installer->getConnection()->newTable(
            $installer->getTable(PriceApproveResourceModel::PRICE_APPROVE_TABLE)
        )->addColumn(
            PriceApproveInterface::ID,
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true,
            ],
            'ID'
        )->addColumn(
            PriceApproveInterface::NAME,
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [
                'unsigned' => true,
                'nullable' => false,
            ],
            'Nome'
        )->addColumn(
            PriceApproveInterface::SKU,
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [
                'unsigned' => true,
                'nullable' => false,
            ],
            'Product SKU'
        )->addColumn(
            PriceApproveInterface::DATETIME,
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            null,
            [
                'unsigned' => true,
                'nullable' => false,
            ],
            'Date of Update'
        )->addColumn(
            PriceApproveInterface::ATTRIBUTE,
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [
                'unsigned' => true,
                'nullable' => false,
            ],
            'Atributo'
        )->addColumn(
            PriceApproveInterface::PRICE_BEFORE,
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [
                'unsigned' => true,
                'nullable' => false,
            ],
            'Price Before'
        )->addColumn(
            PriceApproveInterface::PRICE_AFTER_APPROVE,
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [
                'unsigned' => true,
                'nullable' => false,
            ],
            'Price after approve'
        )->addColumn(
            PriceApproveInterface::USER_ID,
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [
                'unsigned' => true,
                'nullable' => false,
            ],
            'Admin User'
        )->setComment(
            'Approve Price Table'
        );
        $installer->getConnection()->createTable($table);
    }

}
