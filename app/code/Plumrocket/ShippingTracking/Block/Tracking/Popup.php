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
 * @package     Plumrocket_ShippingTracking
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\ShippingTracking\Block\Tracking;

class Popup extends \Magento\Shipping\Block\Tracking\Popup
{
    /**
     * @var \Magento\Shipping\Model\Tracking\Result\StatusFactory
     */
    private $trackingResultFactory;

    /**
     * @var \Plumrocket\ShippingTracking\Helper\Data
     */
    private $dataHelper;

    /**
     * Popup constructor.
     *
     * @param \Plumrocket\ShippingTracking\Helper\Data                      $dataHelper
     * @param \Magento\Shipping\Model\Tracking\Result\StatusFactory         $trackingResultFactory
     * @param \Magento\Framework\View\Element\Template\Context              $context
     * @param \Magento\Framework\Registry                                   $registry
     * @param \Magento\Framework\Stdlib\DateTime\DateTimeFormatterInterface $dateTimeFormatter
     * @param array                                                         $data
     */
    public function __construct(
        \Plumrocket\ShippingTracking\Helper\Data $dataHelper,
        \Magento\Shipping\Model\Tracking\Result\StatusFactory $trackingResultFactory,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Stdlib\DateTime\DateTimeFormatterInterface $dateTimeFormatter,
        array $data = []
    ) {
        $this->trackingResultFactory = $trackingResultFactory;
        $this->dataHelper = $dataHelper;

        parent::__construct($context, $registry, $dateTimeFormatter, $data);
    }

    /**
     * Retrieve array of tracking info
     *
     * @return array
     */
    public function getTrackingInfo()
    {
        $results = parent::getTrackingInfo();
        
        if (!$this->dataHelper->moduleEnabled()) {
            return $results;
        }

        foreach ($results as $shipping => $result) {
            foreach($result as $key => $track) {
                if (!is_object($track)) {
                    continue;
                }

                $carrier = $track->getCarrier();

                if ($this->dataHelper->getSysConfig()->getEnabledMethodByName($carrier)) {
                    $url = $this->getUrl('shippingtracking/result/index', [$carrier => trim($track->getTracking())]);
                    $results[$shipping][$key] = $this->trackingResultFactory->create()->setData($track->getAllData())
                        ->setErrorMessage(null)
                        ->setUrl($url);
                }
            }
        }
        
        return $results;
    }
}