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
 * @package     Plumrocket_Estimateddelivery
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Estimateddelivery\Observer;

abstract class AbstractBeforeSaveTime implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Plumrocket\Estimateddelivery\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    private $localeDate;

    /**
     * @var array
     */
    private $attributeListToChange = [
        'estimated_shipping_date_from',
        'estimated_shipping_date_to',
        'estimated_delivery_date_from',
        'estimated_delivery_date_to',
    ];

    /**
     * CategorySaveBeforeObserver constructor.
     *
     * @param \Plumrocket\Estimateddelivery\Helper\Data            $dataHelper
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     */
    public function __construct(
        \Plumrocket\Estimateddelivery\Helper\Data $dataHelper,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
    ) {
        $this->dataHelper = $dataHelper;
        $this->localeDate = $localeDate;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (! $this->dataHelper->moduleEnabled()) {
            return;
        }

        $object = $observer->getEvent()->getDataObject();

        foreach ($this->attributeListToChange as $attribute) {
            if (null !== $object->getData($attribute)) {
                $object->setData(
                    $attribute,
                    $this->localeDate->convertConfigTimeToUtc($object->getData($attribute))
                );
            }
        }

        return $this;
    }
}
