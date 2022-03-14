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
 * @package     Plumrocket_Ajaxcart
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Ajaxcart\Observer;

class LayoutLoadBeforeObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Plumrocket\Ajaxcart\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Plumrocket\Ajaxcart\Model\Magento\VersionProvider
     */
    private $versionProvider;

    /**
     * LayoutLoadBeforeObserver constructor.
     *
     * @param \Plumrocket\Ajaxcart\Helper\Data                   $dataHelper
     * @param \Plumrocket\Ajaxcart\Model\Magento\VersionProvider $versionProvider
     */
    public function __construct(
        \Plumrocket\Ajaxcart\Helper\Data $dataHelper,
        \Plumrocket\Ajaxcart\Model\Magento\VersionProvider $versionProvider
    ) {
        $this->dataHelper = $dataHelper;
        $this->versionProvider = $versionProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (! $this->dataHelper->moduleEnabled()) {
            return;
        }
        /**
         * @var \Magento\Framework\View\Layout\ProcessorInterface $update
         */
        $update = $observer->getEvent()->getLayout()->getUpdate();
        if ($this->versionProvider->isMagentoVersionBelow('2.3.0')) {
            $update->addUpdate('<head><css src="Magento_Swatches::css/swatches.css"/></head>');
        }

        if (! $this->versionProvider->isMagentoVersionBelow('2.3.2')
            && in_array('prajaxcart_cart_addconfigure_type_bundle', $update->getHandles(), true)
        ) {
            $update->addHandle('prajaxcart_cart_addconfigure_type_bundle_fix_2_3_2');
        }

        if (! $this->versionProvider->isMagentoVersionBelow('2.3.4')
            && in_array('prajaxcart_cart_addconfigure_type_configurable', $update->getHandles(), true)
        ) {
            $update->addHandle('prajaxcart_cart_addconfigure_type_configurable_fix_2.3.4');
        }
    }
}
