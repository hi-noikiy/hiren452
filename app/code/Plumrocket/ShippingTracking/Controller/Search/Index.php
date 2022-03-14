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
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\ShippingTracking\Controller\Search;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * Base Url
     */
    const BASE_URL = 'shippingtracking/index/index';

    /**
     * Result Url
     */
    const RESULT_URL = 'shippingtracking/result/index';

    /**
     * @var \Plumrocket\ShippingTracking\Model\AbstractService
     */
    private $trackingModel;

    /**
     * @var \Plumrocket\ShippingTracking\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * Index constructor.
     *
     * @param \Magento\Framework\App\Action\Context              $context
     * @param \Plumrocket\ShippingTracking\Model\AbstractService $trackingModel
     * @param \Plumrocket\ShippingTracking\Helper\Data           $dataHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Plumrocket\ShippingTracking\Model\AbstractService $trackingModel,
        \Plumrocket\ShippingTracking\Helper\Data $dataHelper
    ) {
        parent::__construct($context);

        $this->messageManager = $context->getMessageManager();
        $this->dataHelper = $dataHelper;
        $this->trackingModel = $trackingModel;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        if (!$this->dataHelper->moduleEnabled()) {
            return $resultRedirect->setPath('404notfound');
        }

        $data = $this->getRequest()->getParams();

        if (isset($data['shippingtracking']['order'])
            && isset($data['shippingtracking']['info'])
        ) {
            $orderId = trim($data['shippingtracking']['order']);
            $info = trim($data['shippingtracking']['info']);
            $data = $this->trackingModel->getTrackingNumberByOrderData($orderId, $info);
            $params = [];

            if (!empty($data)) {
                $carrier = key($data);

                if (isset($data[$carrier])) {
                    $params[$carrier] = $data[$carrier];
                }
            }

            if (!empty($params)) {
                return $resultRedirect->setPath(self::RESULT_URL, $params);
            }

            $this->messageManager->addError(__('Make sure that you have entered the Order Number and phone number (or email address) correctly.'));
        } elseif (isset($data['number']) && isset($data['carrier'])) {
            return $resultRedirect->setPath(self::RESULT_URL, [$data['carrier'] => $data['number']]);
        }

        return  $resultRedirect->setPath(self::BASE_URL);
    }
}