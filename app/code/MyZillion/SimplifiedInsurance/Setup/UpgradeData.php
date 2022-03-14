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

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use MyZillion\SimplifiedInsurance\Helper\Data;

/**
 * Upgrade data file to control database migrations
 */
class UpgradeData implements UpgradeDataInterface
{

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * Constructor
     *
     * @param \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * Upgrade migrations function
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        if (version_compare($context->getVersion(), '1.0.0', '<')) {
            $this->addAttributesToQuote($setup);
            $this->addAttributesToOrder($setup);
        }

        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            // Remove old attributes
            $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'zillion_type');
            $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'zillion_specs');
            $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, Data::IS_INSURABLE);
            $eavSetup->removeAttribute(\Magento\Catalog\Model\Category::ENTITY, Data::IS_INSURABLE);
        }

        if (version_compare($context->getVersion(), '1.0.3', '<')) {
            // Add column if missing
            $this->addQuoteResponseColumn($setup);
        }
    }

    /**
     * Add attributes to quote
     *
     * @param ModuleDataSetupInterface $setup
     * @return void
     */
    protected function addAttributesToQuote($setup)
    {
        $table = $setup->getTable('quote');
        if (!$setup->getConnection()->tableColumnExists($table, Data::CUSTOMER_REQUEST_INSURANCE)) {
            $setup->getConnection()
            ->addColumn(
                $table,
                Data::CUSTOMER_REQUEST_INSURANCE,
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'nullable' => true,
                    'default'  => '0',
                    'unsigned' => true,
                    'comment'  => 'Indicates if the customer requested for insurance',
                ]
            );
        }

        $this->addQuoteResponseColumn($setup);
    }

    /**
     * Add attributes to order
     *
     * @param ModuleDataSetupInterface $setup
     * @return void
     */
    protected function addAttributesToOrder($setup)
    {
        // Install columns table sales
        $table = $setup->getTable('sales_order');
        if (!$setup->getConnection()->tableColumnExists($table, Data::CUSTOMER_REQUEST_INSURANCE)) {
            $setup->getConnection()
            ->addColumn(
                $table,
                Data::CUSTOMER_REQUEST_INSURANCE,
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'nullable' => true,
                    'default'  => '0',
                    'unsigned' => true,
                    'comment'  => 'Indicates if the customer request for insurance',
                ]
            );
        }

        if (!$setup->getConnection()->tableColumnExists($table, Data::ORDER_POST_RESPONSE)) {
            $setup->getConnection()
            ->addColumn(
                $table,
                Data::ORDER_POST_RESPONSE,
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'lenght'   => '2M',
                    'comment'  => 'Save order post endoint response',
                ]
            );
        }
    }

    /**
     * Add quote.offer_response column
     *
     * @param ModuleDataSetupInterface $setup
     * @return void
     */
    protected function addQuoteResponseColumn($setup)
    {
        $table = $setup->getTable('quote');
        if (!$setup->getConnection()->tableColumnExists($table, Data::OFFER_RESPONSE)) {
            $setup->getConnection()
            ->addColumn(
                $table,
                Data::OFFER_RESPONSE,
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'lenght'   => '2M',
                    'comment'  => 'Offer request response',
                ]
            );
        }
    }
}
