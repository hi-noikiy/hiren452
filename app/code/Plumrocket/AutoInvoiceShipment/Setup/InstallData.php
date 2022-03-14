<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package   Plumrocket_AutoInvoiceShipment
 * @copyright Copyright (c) 2017 Plumrocket Inc. (http://www.plumrocket.com)
 * @license   http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\AutoInvoiceShipment\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Plumrocket\AutoInvoiceShipment\Model\Invoicerules;
use Plumrocket\AutoInvoiceShipment\Model\Shipmentrules;

class InstallData implements InstallDataInterface
{
    /**
     * @var \Plumrocket\AutoInvoiceShipment\Model\InvoicerulesFactory
     */
    protected $invoicerulesFactory;

    /**
     * @var \Plumrocket\AutoInvoiceShipment\Model\ShipmentrulesFactory
     */
    protected $shipmentrulesFactory;

    /**
     * @var \Magento\CatalogRule\Model\Rule\WebsitesOptionsProvider
     */
    protected $websitesOptionsProvider;

    /**
     * @var \Magento\CatalogRule\Model\Rule\CustomerGroupsOptionsProvider
     */
    protected $customerGroupsOptionsProvider;

    /**
     * InstallData constructor.
     *
     * @param \Plumrocket\AutoInvoiceShipment\Model\InvoicerulesFactory     $invoicerulesFactory
     * @param \Plumrocket\AutoInvoiceShipment\Model\ShipmentrulesFactory    $shipmentrulesFactory
     * @param \Magento\CatalogRule\Model\Rule\WebsitesOptionsProvider       $websitesOptionsProvider
     * @param \Magento\CatalogRule\Model\Rule\CustomerGroupsOptionsProvider $customerGroupsOptionsProvider
     * @param \Magento\Framework\App\State                                  $state
     */
    public function __construct(
        \Plumrocket\AutoInvoiceShipment\Model\InvoicerulesFactory $invoicerulesFactory,
        \Plumrocket\AutoInvoiceShipment\Model\ShipmentrulesFactory $shipmentrulesFactory,
        \Magento\CatalogRule\Model\Rule\WebsitesOptionsProvider $websitesOptionsProvider,
        \Magento\CatalogRule\Model\Rule\CustomerGroupsOptionsProvider $customerGroupsOptionsProvider,
        \Magento\Framework\App\State $state
    ) {
        $this->invoicerulesFactory              = $invoicerulesFactory;
        $this->shipmentrulesFactory             = $shipmentrulesFactory;
        $this->websitesOptionsProvider          = $websitesOptionsProvider;
        $this->customerGroupsOptionsProvider    = $customerGroupsOptionsProvider;
        try {
            $state->setAreaCode('adminhtml');
        } catch (\Exception $e) {}
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $websitesOptions = $this->websitesOptionsProvider->toOptionArray();
        $websitesIdsArray = [];
        foreach ($websitesOptions as $websitesOption) {
            if (isset($websitesOption['value'])) {
                $websitesIdsArray[] = $websitesOption['value'];
            }
        }
        $websitesIds = implode(',', $websitesIdsArray);

        $customerGroupsOptions = $this->customerGroupsOptionsProvider->toOptionArray();
        $customerGroupsIdsArray = [];
        foreach ($customerGroupsOptions as $groupsOption) {
            if (isset($groupsOption['value'])) {
                $customerGroupsIdsArray[] = $groupsOption['value'];
            }
        }
        $customerGroupsIds = implode(',', $customerGroupsIdsArray);

        $conditions = [
            'type'               => 'Plumrocket\AutoInvoiceShipment\Model\AbstractRules\Condition\Combine',
            'attribute'          => null,
            'operator'           => null,
            'value'              => '1',
            'is_value_processed' => null,
            'aggregator'         => 'all',
        ];

        $autoInvoiceDefaultData = [
            'name'             => 'Default Auto Invoice Rule',
            'status'           => Invoicerules::STATUS_DISABLED,
            'create_invoice'   => Invoicerules::CREATE_INVOICE_AFTER_CREATED,
            'capture_amount'   => Invoicerules::CAPTURE_ONLINE,
            'websites'         => $websitesIds,
            'customer_groups'  => $customerGroupsIds,
            'comment'          => Invoicerules::DEFAULT_COMMENT,
            'comment_to_email' => Invoicerules::APPEND_COMMENT_TO_EMAIL_NO,
            'conditions'       => $conditions,
        ];

        $autoShipmentDefaultData = [
            'name'             => 'Default Auto Shipment Rule',
            'status'           => Shipmentrules::STATUS_DISABLED,
            'create_shipment'  => Shipmentrules::CREATE_AFTER_INVOICE_CREATED,
            'websites'         => $websitesIds,
            'customer_groups'  => $customerGroupsIds,
            'comment'          => Shipmentrules::DEFAULT_COMMENT,
            'comment_to_email' => Shipmentrules::APPEND_COMMENT_TO_EMAIL_NO,
            'conditions'       => $conditions,
        ];

        /**
         * @var $autoInvoiceDefault Invoicerules
         */
        $autoInvoiceDefault = $this->invoicerulesFactory->create();
        $autoInvoiceDefault->setData($autoInvoiceDefaultData)->save();

        /**
         * @var $autoShipmentDefault Shipmentrules
         */
        $autoShipmentDefault = $this->shipmentrulesFactory->create();
        $autoShipmentDefault->setData($autoShipmentDefaultData)->save();
    }
}
