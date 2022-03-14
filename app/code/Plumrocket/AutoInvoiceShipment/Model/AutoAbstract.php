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

use Plumrocket\AutoInvoiceShipment\Helper\Data;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Sales\Model\Order;
use Magento\Framework\DB\Transaction;
use Magento\Store\Model\StoreManagerInterface;

abstract class AutoAbstract extends AbstractModel
{
    /**
     * Enable|Disable log
     *
     * @var bool
     */
    protected $debugMode = false;

    /**
     * Current Rule
     *
     * @var Invoicerules | Shipmentrules | null
     */
    protected $currentRule = null;

    /**
     * AutoInvoiceShipment helper
     *
     * @var Data
     */
    protected $dataHelper;

    /**
     * Necessary for save invoice|shipment
     *
     * @var Transaction
     */
    protected $transactionSave;

    /**
     * Use for get website id
     *
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * AutoAbstract constructor.
     *
     * @param Context                $context
     * @param Registry               $registry
     * @param OrderCollectionFactory $orderCollectionFactory
     * @param Transaction            $transactionSave
     * @param Data                   $dataHelper
     * @param StoreManagerInterface  $storeManager
     * @param AbstractResource|null  $resource
     * @param AbstractDb|null        $resourceCollection
     * @param array                  $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        OrderCollectionFactory $orderCollectionFactory,
        Transaction $transactionSave,
        Data $dataHelper,
        StoreManagerInterface $storeManager,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->transactionSave  = $transactionSave;
        $this->dataHelper       = $dataHelper;
        $this->storeManager     = $storeManager;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Make Invoice or Shipment
     *
     * @param Order                                                                              $order
     * @param bool                                                                               $skipValidCreateAfter
     * @param null | \Plumrocket\AutoInvoiceShipment\Model\ResourceModel\Invoicerules\Collection $rulesCollection
     *
     * @return bool
     */
    abstract public function make($order);

    /**
     * @return bool
     */
    abstract public function validate($order, $skipValidCreateAfter, $rulesCollection = null): bool;

    /**
     * Logger for debug
     *
     * @param mixed $message
     */
    public function log($message)
    {
        if ($this->debugMode) {
            $this->_logger->debug($message);
        }
    }

    /**
     * @return string
     */
    protected function getComment()
    {
        return (string) $this->currentRule->getComment();
    }

    /**
     * @return bool
     */
    protected function useCustomerNotification(): bool
    {
        return (bool) $this->currentRule->canAddComment();
    }
}
