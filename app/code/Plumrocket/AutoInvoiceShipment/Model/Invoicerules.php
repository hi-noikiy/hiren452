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

namespace Plumrocket\AutoInvoiceShipment\Model;

use Plumrocket\AutoInvoiceShipment\Model\ResourceModel\Invoicerules\Collection as InvoicerulesCollection;
use Magento\Sales\Model\Order;

/**
 * @method InvoicerulesCollection getCollection()
 * @method int getCreateInvoice()
 * @method int getCaptureAmount()
 */
class Invoicerules extends AbstractRules
{
    const CREATE_INVOICE_AFTER_CREATED = 1;
    const CREATE_INVOICE_AFTER_SHIPPED = 2;

    const CAPTURE_ONLINE  = 2;
    const CAPTURE_OFFLINE = 1;
    const CAPTURE_NOT     = 0;

    const DEFAULT_COMMENT = 'Invoice Created Automatically';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'invoicerules';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Plumrocket\AutoInvoiceShipment\Model\ResourceModel\Invoicerules::class);
    }

    /**
     * @param null | int | array $websiteId
     * @return InvoicerulesCollection
     */
    public function getActiveRules($websiteId = null)
    {
        return $this->getCollection()
            ->addEnabledFilter()
            ->addWebsiteFilter($websiteId)
            ->addSortByPriority();
    }

    /**
     * @param Order $order
     * @param bool  $skipValidCreateAfter
     *
     * @return bool
     */
    public function preValidate(Order $order, $skipValidCreateAfter)
    {
        $success = true;
        // validate Create Invoice after
        if ($this->getCreateInvoice() == self::CREATE_INVOICE_AFTER_SHIPPED) {
            $success = $skipValidCreateAfter || $order->hasShipments();
        }

        return parent::_preValidate($order, $success);
    }

    /**
     * Retrieve Serialized Conditions
     * Deprecated and Need to be removed in future releases
     * added for compatibility with 2.1.x and 2.2.x
     * @return string
     */
    public function getConditionsSerialized()
    {
        $value = $this->getData('conditions_serialized');

        if (isset($this->serializer)) {
            try {
                $uv = $this->phpSerializer->unserialize($value);
                $value = $this->serializer->serialize($uv);
            } catch (\Exception $e) {}
        }

        return $value;
    }

    /**
     * Retrieve Serialized Actions
     * Deprecated and Need to be removed in future releases
     * added for compatibility with 2.1.x and 2.2.x
     * @return string
     */
    public function getActionsSerialized()
    {
        $value = $this->getData('actions_serialized');

        if (isset($this->serializer)) {
            try {
                $uv = $this->phpSerializer->unserialize($value);
                $value = $this->serializer->serialize($uv);
            } catch (\Exception $e) {}
        }

        return $value;
    }

    /**
     * Get capture case
     *
     * @return string
     */
    public function getCaptureCase()
    {
        switch ($this->getCaptureAmount()) {
            case self::CAPTURE_ONLINE:
                $captureCase = Order\Invoice::CAPTURE_ONLINE;
                break;
            case self::CAPTURE_OFFLINE:
                $captureCase = Order\Invoice::CAPTURE_OFFLINE;
                break;
            case self::CAPTURE_NOT:
                $captureCase = Order\Invoice::NOT_CAPTURE;
                break;
            default:
                $captureCase = Order\Invoice::CAPTURE_ONLINE;
        }

        return $captureCase;
    }
}
