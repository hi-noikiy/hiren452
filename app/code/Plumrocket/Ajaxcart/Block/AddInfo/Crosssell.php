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
 * @package     Plumrocket_Ajaxcart v2.x.x
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Ajaxcart\Block\AddInfo;

use Magento\Catalog\Block\Product\Context;
use Magento\Checkout\Model\Session;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\Product\LinkFactory;
use Magento\Quote\Model\Quote\Item\RelatedProducts;
use Magento\CatalogInventory\Helper\Stock as StockHelper;
use Plumrocket\Ajaxcart\Helper\Data as DataHelper;

/**
 * Class Crosssell
 * @package Plumrocket\Ajaxcart\Block\AddInfo
 */
class Crosssell extends \Magento\Checkout\Block\Cart\Crosssell
{
    protected $_maxItemCount = 6;

    /**
     * @var \Plumrocket\Ajaxcart\Helper\Data
     */
    protected $dataHelper;

    /**
     * Crosssell constructor
     *
     * @param \Magento\Catalog\Block\Product\Context          $context
     * @param \Magento\Checkout\Model\Session                 $checkoutSession
     * @param \Magento\Catalog\Model\Product\Visibility       $productVisibility
     * @param \Magento\Catalog\Model\Product\LinkFactory      $productLinkFactory
     * @param \Magento\Quote\Model\Quote\Item\RelatedProducts $itemRelationsList
     * @param \Magento\CatalogInventory\Helper\Stock          $stockHelper
     * @param \Plumrocket\Ajaxcart\Helper\Data                $dataHelper
     * @param array                                           $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\Catalog\Model\Product\LinkFactory $productLinkFactory,
        \Magento\Quote\Model\Quote\Item\RelatedProducts $itemRelationsList,
        \Magento\CatalogInventory\Helper\Stock $stockHelper,
        \Plumrocket\Ajaxcart\Helper\Data $dataHelper,
        array $data = []
    ) {
        $this->dataHelper = $dataHelper;
        parent::__construct(
            $context,
            $checkoutSession,
            $productVisibility,
            $productLinkFactory,
            $itemRelationsList,
            $stockHelper,
            $data
        );
    }

    /**
     * Get crosssell items
     *
     * @return array
     */
    public function getCrosssellItems()
    {
        if ($this->dataHelper->isTargetRuleModule()) {
            $items = $this->getLayout()
                ->createBlock('Magento\TargetRule\Block\Checkout\Cart\Crosssell')
                ->getItemCollection();
        } else {
            $items = $this->getItems();
        }

        return $items;
    }
}
