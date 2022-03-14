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
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Ajaxcart\Plugin\Catalog\Block\Product;

use Plumrocket\Ajaxcart\Helper\Data as DataHelper;
use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Framework\Registry;

/**
 * Class AbstractProductPlugin
 *
 * @package Plumrocket\Ajaxcart\Plugin\Catalog\Block\Product
 */
class AbstractProductPlugin
{
    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * AbstractProductPlugin constructor.
     *
     * @param DataHelper $dataHelper
     * @param Registry   $registry
     */
    public function __construct(
        DataHelper $dataHelper,
        Registry $registry
    ) {
        $this->dataHelper = $dataHelper;
        $this->registry = $registry;
    }

    /**
     * @param AbstractProduct $abstractProduct
     * @param                 $html
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterToHtml(AbstractProduct $abstractProduct, $html)
    {
        if (
            !$this->dataHelper->moduleEnabled()
            || $this->registry->registry('pr_ajaxcart_plugin')
            || $this->dataHelper->preventAjaxacartJs()
            || $this->dataHelper->isManualMode()
        ) {
            return $html;
        }

        $blockJsHtml = $abstractProduct->getLayout()
            ->createBlock('Plumrocket\Ajaxcart\Block\Js')
            ->toHtml();

        $this->registry->register('pr_ajaxcart_plugin', 1);

        return $html . $blockJsHtml;
    }
}
