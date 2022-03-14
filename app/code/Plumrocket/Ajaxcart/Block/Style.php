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

namespace Plumrocket\Ajaxcart\Block;

/**
 * Class Style
 *
 * @package Plumrocket\Ajaxcart\Block
 */
class Style extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Plumrocket\Ajaxcart\Helper\Data
     */
    private $dataHelper;

    /**
     * Style constructor.
     *
     * @param \Plumrocket\Ajaxcart\Helper\Data         $dataHelper
     * @param \Magento\Framework\View\Element\Template $context
     * @param array                                    $data
     */
    public function __construct(
        \Plumrocket\Ajaxcart\Helper\Data $dataHelper,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->dataHelper = $dataHelper;

        parent::__construct($context, $data);
    }

    /**
     * get styles from admin
     * @param  string $type block of styles
     * @return object
     */
    public function getStyles($type)
    {
        $styles = $this->_scopeConfig->getValue(
            'prajaxcart/design/' . $type,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        return (object)$styles;
    }

    /**
     * @return bool
     */
    public function styleEnable()
    {
        return $this->dataHelper->isAutomaticMode();
    }
}
