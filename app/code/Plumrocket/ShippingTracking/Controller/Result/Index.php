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

namespace Plumrocket\ShippingTracking\Controller\Result;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $resultPageFactory;

    /**
     * @var \Plumrocket\ShippingTracking\Model\AbstractService
     */
    private $trackingModel;

    /**
     * @var \Plumrocket\ShippingTracking\Helper\Data
     */
    private $dataHelper;

    /**
     * Index constructor.
     *
     * @param \Magento\Framework\App\Action\Context                $context
     * @param \Magento\Framework\View\Result\PageFactory           $resultPageFactory
     * @param \Plumrocket\ShippingTracking\Model\AbstractService   $trackingModel
     * @param \Plumrocket\ShippingTracking\Helper\Data             $dataHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Plumrocket\ShippingTracking\Model\AbstractService $trackingModel,
        \Plumrocket\ShippingTracking\Helper\Data $dataHelper
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->trackingModel = $trackingModel;
        $this->dataHelper = $dataHelper;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if (!$this->dataHelper->moduleEnabled()) {
            return $this->resultRedirectFactory->create()->setPath('404notfound');
        }

        $resultPage = $this->resultPageFactory->create();
        $data = $this->getRequest()->getParams();

        if (!empty($data)) {
            $carrier = key($data);
            if (isset($data[$carrier])) {
                $trackingNumber = $data[$carrier];
                $resultPage->getLayout()->getBlock('pr_shippingtracking_result')
                    ->setData([
                        'carrier' => $carrier,
                        'tracking_number' => $trackingNumber,
                        'order_ids' => $this->trackingModel->getOrderIdsByTrackingNumber($trackingNumber)
                    ]);
            }
        }

        return $resultPage;
    }
}