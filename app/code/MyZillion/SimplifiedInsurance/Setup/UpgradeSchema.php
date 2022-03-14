<?php
/**
 * Copyright (c) 2020, Zillion Insurance Services, Inc.
 * All rights reserved.
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *   * Redistributions of source code must retain the above copyright notice,
 *     this list of conditions and the following disclaimer.
 *   * Redistributions in binary form must reproduce the above copyright notice,
 *     this list of conditions and the following disclaimer in the documentation
 *     and/or other materials provided with the distribution.
 *   * Neither the name of Zend Technologies USA, Inc. nor the names of its
 *     contributors may be used to endorse or promote products derived from this
 *     software without specific prior written permission.
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @package myzillion
 * @subpackage module-simplified-insurance
 * @author Maximo Marucci | Serfe Team <info@serfe.com>
 * @copyright 2020 Zillion Insurance Services, Inc.
 * @since 1.0.0
 */

namespace MyZillion\SimplifiedInsurance\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

/**
 * Uninstall script to remove added columns
 */
class UpgradeSchema implements UpgradeSchemaInterface
{

    /**
     * Upgrade
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        if (version_compare($context->getVersion(), '1.0.4', '<')) {
            // Create table shipment_post_request
            $this->createTableShipmentPostRequest($setup);
        }
    }

    /**
     * create table shipment_post_request
     *
     * @param ModuleDataSetupInterface $setup
     * @return void
     */
    protected function createTableShipmentPostRequest($setup)
    {
        $tableName = \MyZillion\SimplifiedInsurance\Model\ResourceModel\ShipmentPostRequest::TABLE_NAME;

        $installer = $setup;
        $installer->startSetup();
        if (!$installer->tableExists($tableName)) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable($tableName)
            )
                ->addColumn(
                    'entity_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'nullable' => false,
                        'primary'  => true,
                        'unsigned' => true,
                    ],
                    'Map entity id'
                )
                ->addColumn(
                    'shipment_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false],
                    'Magento shipment id'
                )
                    ->addColumn(
                        'order_id',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        null,
                        ['nullable' => false],
                        'Magento order id'
                    )
                ->addColumn(
                    'post_order_status',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    40,
                    ['nullable => false'],
                    'Post order creation status'
                )
                ->setComment('Post Order Status Table');
            $installer->getConnection()->createTable($table);
        }

        $installer->endSetup();
    }
}
