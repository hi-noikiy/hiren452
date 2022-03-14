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
 * @author Serfe <info@serfe.com>
 * @copyright 2020 Zillion Insurance Services, Inc.
 * @since 1.0.0
 */

namespace MyZillion\SimplifiedInsurance\Setup;

use Magento\Framework\Setup\UninstallInterface;
use MyZillion\SimplifiedInsurance\Helper\Data;

// phpcs:disable Generic.NamingConventions.ConstructorName.OldStyle

/**
 * Uninstall script to remove added columns
 */
class Uninstall implements UninstallInterface
{

    /**
     * Uninstall Method
     * @param  \Magento\Framework\Setup\SchemaSetupInterface    $setup
     * @param  \Magento\Framework\Setup\ModuleContextInterface $context
     * @return void
     */
    public function uninstall(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        $setup->startSetup();

        // Uninstall columns table quote
        $table = $setup->getTable('quote');
        $setup->getConnection()
        ->dropColumn($table, Data::CUSTOMER_REQUEST_INSURANCE)
        ->dropColumn($table, Data::OFFER_RESPONSE);

        // Uninstall columns table sales_order
        $table = $setup->getTable('sales_order');
        $setup->getConnection()
        ->dropColumn($table, Data::CUSTOMER_REQUEST_INSURANCE)
        ->dropColumn($table, Data::ORDER_POST_RESPONSE);

        $this->dropTableShipmentPostRequest($setup);

        $setup->endSetup();
    }

    /**
     * drop table shipment_post_request
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @return void
     */
    protected function dropTableShipmentPostRequest($setup)
    {
        $tableName = \MyZillion\SimplifiedInsurance\Model\ResourceModel\ShipmentPostRequest::TABLE_NAME;

        $connection = $setup->getConnection();
        $connection->dropTable(
            $connection->getTableName($tableName)
        );
    }
}
