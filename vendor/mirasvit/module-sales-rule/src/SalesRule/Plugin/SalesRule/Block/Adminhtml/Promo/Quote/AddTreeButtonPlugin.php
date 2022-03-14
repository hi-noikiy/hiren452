<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-sales-rule
 * @version   1.0.16
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SalesRule\Plugin\SalesRule\Block\Adminhtml\Promo\Quote;

use Magento\Backend\Model\UrlInterface;

class AddTreeButtonPlugin
{
    /**
     * @var bool
     */
    private static $isAdded = false;

    /**
     * @var UrlInterface
     */
    private        $urlBuilder;

    /**
     * AddTreeButtonPlugin constructor.
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        UrlInterface $urlBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @param \Magento\SalesRule\Block\Adminhtml\Promo\Quote $subject
     * @param object                                         $result
     *
     * @return string
     */
    public function afterAddButton($subject, $result = null)
    {
        if (self::$isAdded) {
            return $result;
        }

        self::$isAdded = true;

        $url = $this->urlBuilder->getUrl('sales_rule/rule/tree');
        $subject->addButton('tree', [
            'label'   => __('Visualize Rules'),
            'onclick' => 'setLocation(\'' . $url . '\')',
            'class'   => 'secondary',
        ]);

        return $result;
    }
}