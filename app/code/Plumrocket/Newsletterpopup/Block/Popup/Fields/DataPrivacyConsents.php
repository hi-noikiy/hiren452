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
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Newsletterpopup\Block\Popup\Fields;

use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Customer\Block\Widget\AbstractWidget;
use Magento\Customer\Helper\Address;
use Magento\Framework\View\Element\Template\Context;
use Plumrocket\Base\Helper\Base;
use Plumrocket\Newsletterpopup\Helper\Config;

class DataPrivacyConsents extends AbstractWidget
{
    /**
     * @var \Plumrocket\Newsletterpopup\Helper\Config
     */
    private $config;

    /**
     * DataPrivacyConsents constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Helper\Address                 $addressHelper
     * @param \Magento\Customer\Api\CustomerMetadataInterface  $customerMetadata
     * @param \Plumrocket\Newsletterpopup\Helper\Config        $config
     * @param array                                            $data
     */
    public function __construct(
        Context $context,
        Address $addressHelper,
        CustomerMetadataInterface $customerMetadata,
        Config $config,
        array $data = []
    ) {
        parent::__construct($context, $addressHelper, $customerMetadata, $data);
        $this->config = $config;
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if (Base::MODULE_STATUS_ENABLED === $this->config->getModuleStatus('GDPR')) {
            $this->setTemplate('Plumrocket_GDPR::x-init/location_checkbox_list.phtml')
                 ->setData('locationKey', 'newsletter')
                 ->setData('scope', 'newsletter.popup.data.privacy.consents');

            // disable opening in popup to avoid conflicts between z-index
            $this->setData('denyToOpenCmsInPopup', true);

            // All popups have labels off in their own styles, so we're adding inline style to checkboxes
            $this->setData('checkboxLabelStyle', 'display: initial;');
        }

        return parent::_toHtml();
    }
}
