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

namespace Plumrocket\Newsletterpopup\Controller\Adminhtml\Integration\Lists;

/**
 * Class Activecampaign
 */
class Activecampaign extends \Plumrocket\Newsletterpopup\Controller\Adminhtml\Integration
{
    /**
     * Frontend label for integration
     */
    const RESPONSE_FRONTEND_LABEL = 'ActiveCampaign';

    /**
     * Test constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Plumrocket\Newsletterpopup\Model\Integration\ActiveCampaign $serviceModel
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Plumrocket\Newsletterpopup\Model\Integration\ActiveCampaign $serviceModel
    ) {
        $this->serviceModel = $serviceModel;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        parent::execute();
        $this->responseFrontendLabel = self::RESPONSE_FRONTEND_LABEL;

        try {
            $lists = $this->serviceModel->getAllLists();
            $result = [];

            foreach ($lists as $key => $value) {
                $result[] = [
                    'key' => $key,
                    'value' => $value,
                ];
            }

            return $this->getResultSuccess(__('Success Loading Lists'), $result);
        } catch (\Exception $e) {
            return $this->getResultError();
        }
    }
}
