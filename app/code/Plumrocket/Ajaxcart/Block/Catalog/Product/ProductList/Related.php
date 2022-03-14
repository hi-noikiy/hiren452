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

namespace Plumrocket\Ajaxcart\Block\Catalog\Product\ProductList;

/**
 * Class Related
 * @package Plumrocket\Ajaxcart\Block\Catalog\Product\ProductList
 */
class Related extends \Magento\Catalog\Block\Product\AbstractProduct
{
    /**
     * @var \Plumrocket\Ajaxcart\Helper\Data
     */
    protected $dataHelper;

    /**
     * Related constructor
     *
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Plumrocket\Ajaxcart\Helper\Data       $dataHelper
     * @param array                                  $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Plumrocket\Ajaxcart\Helper\Data $dataHelper,
        array $data = []
    ) {
        $this->dataHelper = $dataHelper;
        parent::__construct($context, $data);
    }

    /**
     * Get related items
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|array
     */
    public function getRelatedItems()
    {
        if ($this->dataHelper->isTargetRuleModule()) {
            $items = $this->getLayout()
                ->createBlock('Magento\TargetRule\Block\Catalog\Product\ProductList\Related')
                ->getAllItems();
        } else {
            $items = $this->getLayout()
                ->createBlock('Magento\Catalog\Block\Product\ProductList\Related')
                ->getItems();
        }

        return $items;
    }
}
