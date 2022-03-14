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
 * @package     Plumrocket_Newsletterpopup
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Newsletterpopup\Block\Adminhtml\System\Config\Form\Integration\ConstantContact;

class CustomFields extends \Plumrocket\Newsletterpopup\Block\Adminhtml\System\Config\Form\Integration\CustomFields
{
    /**
     * @var \Plumrocket\Newsletterpopup\Helper\Config
     */
    private $configHelper;

    /**
     * CustomFields constructor.
     *
     * @param \Plumrocket\Newsletterpopup\Helper\Config $configHelper
     * @param \Magento\Backend\Block\Template\Context   $context
     * @param array                                     $data
     */
    public function __construct(
        \Plumrocket\Newsletterpopup\Helper\Config $configHelper,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->configHelper = $configHelper;
    }

    /**
     * Determine if button is disabled
     *
     * @return boolean
     */
    protected function isDisabled() // @codingStandardsIgnoreLine
    {
        return ! $this->configHelper->getConstantContactApiKey()
            || ! $this->configHelper->getConstantContactSecret()
            || ! $this->configHelper->getConstantContactAccessToken();
    }
}
