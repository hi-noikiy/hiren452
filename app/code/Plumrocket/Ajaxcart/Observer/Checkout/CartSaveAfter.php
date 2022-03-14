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
 * @package     Plumrocket Ajaxcart v2.x.x
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Ajaxcart\Observer\Checkout;

use Plumrocket\Ajaxcart\Helper\Data as DataHelper;

class CartSaveAfter implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var DataHelper $dataHelper
     */
    protected $dataHelper;

    //TODO: Discover why static
    static $quoteItems = [];

    public function __construct(
        DataHelper $dataHelper
    ) {
        $this->dataHelper = $dataHelper;
    }

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        $observerData = $observer->getData();

        if (empty($observerData['cart'])) {
            $this->salesQuoteProductAddAfter($observer);
        } else {
            $this->checkoutCartSaveAfter($observer);
        }
        return  $observer;
    }

    protected function salesQuoteProductAddAfter($observer)
    {
        foreach($observer->getItems() as $quoteItem) {
            self::$quoteItems[] = $quoteItem;
        }
    }

    protected function checkoutCartSaveAfter($observer)
    {
        $items = [];
        foreach(self::$quoteItems as  $quoteItem) {
            if ($quoteItem->getParentItem()) {
                $quoteItem = $quoteItem->getParentItem();
            }
            $items[$quoteItem->getId()] = $quoteItem;
        }
        $this->dataHelper->newAddedQuoteItems($items);
    }
}
