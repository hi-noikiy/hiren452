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

namespace Plumrocket\Newsletterpopup\Observer;

/**
 * Class PredispatchAdminhtmlSystemConfigEditObserver
 */
class PredispatchAdminhtmlSystemConfigEditObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;

    /**
     * @var \Plumrocket\Newsletterpopup\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Plumrocket\Newsletterpopup\Model\Integration\ActiveCampaign
     */
    private $activeCampaign;

    /**
     * PredispatchAdminhtmlSystemConfigEditObserver constructor.
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Plumrocket\Newsletterpopup\Helper\Data $dataHelper
     * @param \Plumrocket\Newsletterpopup\Model\Integration\ActiveCampaign $activeCampaign
     */
    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Plumrocket\Newsletterpopup\Helper\Data $dataHelper,
        \Plumrocket\Newsletterpopup\Model\Integration\ActiveCampaign $activeCampaign
    ) {
        $this->dataHelper = $dataHelper;
        $this->messageManager = $messageManager;
        $this->activeCampaign = $activeCampaign;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->dataHelper->moduleEnabled()
            && $this->activeCampaign->isEnable()
            && empty($this->activeCampaign->getAllLists())
        ) {
            $this->messageManager->addWarningMessage(
                __('Active Campaign newsletter subscription will not work until you create at least one Contact List.')
            );
        }
    }
}
