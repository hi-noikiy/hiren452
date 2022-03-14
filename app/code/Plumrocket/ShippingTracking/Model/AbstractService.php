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

namespace Plumrocket\ShippingTracking\Model;

use Magento\Sales\Api\Data\ShipmentTrackInterface;

class AbstractService
{
    /**
     * @var string
     */
    protected $locationColumnName = 'Location';

    /**
     * @var string
     */
    protected $dateColumnName = 'Date';

    /**
     * @var string
     */
    protected $timeColumnName = 'Time';

    /**
     * @var string
     */
    protected $statusColumnName = 'Status';

    /**
     * @var \Plumrocket\ShippingTracking\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Magento\Framework\HTTP\Client\Curl
     */
    protected $curl;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Shipment\Track\CollectionFactory
     */
    protected $trackingCollection;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Directory\Model\CountryFactory
     */
    protected $countryFactory;

    /**
     * AbstractService constructor.
     *
     * @param \Plumrocket\ShippingTracking\Helper\Data                                  $dataHelper
     * @param \Magento\Framework\HTTP\Client\Curl                                       $curl
     * @param \Magento\Framework\Json\Helper\Data                                       $jsonHelper
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory                $orderCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Shipment\Track\CollectionFactory $trackingCollection
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface                      $timezone
     * @param \Magento\Store\Model\StoreManagerInterface                                $storeManager
     * @param \Magento\Directory\Model\CountryFactory                                   $countryFactory
     */
    public function __construct(
        \Plumrocket\ShippingTracking\Helper\Data $dataHelper,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\Shipment\Track\CollectionFactory $trackingCollection,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Directory\Model\CountryFactory $countryFactory
    ) {
        $this->dataHelper = $dataHelper;
        $this->curl = $curl;
        $this->jsonHelper = $jsonHelper;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->trackingCollection = $trackingCollection->create();
        $this->timezone = $timezone;
        $this->storeManager = $storeManager;
        $this->countryFactory = $countryFactory;
    }

    /**
     * @return \Plumrocket\ShippingTracking\Helper\Config
     */
    public function getConfig()
    {
        return $this->dataHelper->getSysConfig();
    }

    /**
     * @return int
     */
    public function getCurrentStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }

    /**
     * @param $params
     * @return mixed
     */
    public function call($params)
    {
        $ch = curl_init();

        foreach ($params as $param) {
            curl_setopt($ch, $param['option'], $param['value']);
        }

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    /**
     * @param $url
     * @param $params
     * @return mixed
     */
    public function fileGetContents($url, $params) 
    {
        $context = stream_context_create([
            'http' => $params
        ]);

        return file_get_contents($url, false, $context);
    }

    /**
     * @param $orderId
     * @param $identifier
     * @return array
     */
    public function getTrackingNumberByOrderData($orderId, $identifier)
    {
        $trackNumbers = [];
        $isTelephone = false;

        $orderCollection = $this->orderCollectionFactory->create()->addAttributeToSelect('*')
            ->addFieldToFilter('increment_id', ['eq' => $orderId]);

        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            $orderCollection->addFieldToFilter('customer_email', ['eq' => $identifier]);
        } else {
            $isTelephone = true;
        }

        if ($orderCollection->getSize() == 1) {
            $order = $orderCollection->getFirstItem();
            if ($isTelephone) {
                $telephone = $order->getShippingAddress()->getTelephone();
                if ($telephone == $identifier) {
                    $tracksCollection = $order->getTracksCollection();
                } else {
                    $tracksCollection = null;
                }
            } else {
                $tracksCollection = $order->getTracksCollection();
            }

            if (!empty($tracksCollection)) {
                foreach ($tracksCollection->getItems() as $track) {
                    $trackNumbers[$track->getCarrierCode()] = $track->getTrackNumber();
                }
            }
        }

        return $trackNumbers;
    }

    /**
     * @param $trackNumber
     * @return array
     */
    public function getOrderIdsByTrackingNumber($trackNumber)
    {
        $this->trackingCollection->addFieldToFilter(
            ShipmentTrackInterface::TRACK_NUMBER,
            $trackNumber
        );

        $ordersData = $this->trackingCollection->getData();
        $result = [];

        foreach ($ordersData as $order) {
            if (isset($order['order_id'])) {
                $result[] = $order['order_id'];
            }
        }

        return $result;
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTimeFormat()
    {
        return __('g:i a');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getDateFormat()
    {
        return __('m/d/Y');
    }

    /**
     * @param $countryCode
     * @return string
     */
    public function getCountryName($countryCode)
    {
        $country = $this->countryFactory->create()->loadByCode($countryCode);
        if ($country && $country->getName()) {
            return $country->getName();
        }

        return $countryCode;
    }
}