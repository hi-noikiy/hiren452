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
 * @package     Plumrocket SMTP
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Smtp\Block\Adminhtml\Grid\Button;

class ClearLog implements \Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface
{
    /**
     * @var \Magento\Backend\Block\Widget\Context
     */
    private $context;

    /**
     * ClearLog constructor.
     *
     * @param \Magento\Backend\Block\Widget\Context $context
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context
    ) {
        $this->context = $context;
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Clear Log'),
            'on_click' => 'deleteConfirm(\'' . __(
                'Are you sure you want to remove all the logs?'
            ) . '\', \'' . $this->getClearLogUrl() . '\')',
            'class' => 'delete',
            'sort_order' => 0
        ];
    }

    /**
     * Get URL
     *
     * @return string
     */
    public function getClearLogUrl()
    {
        return $this->context->getUrlBuilder()->getUrl('prsmtp/log/remove', []);
    }
}
