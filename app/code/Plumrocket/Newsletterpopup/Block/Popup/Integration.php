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

namespace Plumrocket\Newsletterpopup\Block\Popup;

use Plumrocket\Newsletterpopup\Model\Config\Source\SubscriptionMode;

/**
 * Class Integration
 */
class Integration extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'popup/integration.phtml';

    /**
     * @var \Plumrocket\Newsletterpopup\Helper\Adminhtml
     */
    private $adminhtmlHelper;

    /**
     * @var \Plumrocket\Newsletterpopup\Model\IntegrationRepositoryInterface
     */
    private $integrationRepository;

    /**
     * @var \Plumrocket\Newsletterpopup\Model\ResourceModel\MailchimpList\CollectionFactory
     */
    private $listCollectionFactory;

    /**
     * @var array
     */
    private $allowedModes = [
        SubscriptionMode::ONE_LIST_RADIO,
        SubscriptionMode::ONE_LIST_SELECT,
        SubscriptionMode::MUPTIPLE_LIST,
    ];

    /**
     * Integration constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Plumrocket\Newsletterpopup\Helper\Adminhtml $adminhtmlHelper
     * @param \Plumrocket\Newsletterpopup\Model\IntegrationRepositoryInterface $integrationRepository
     * @param \Plumrocket\Newsletterpopup\Model\ResourceModel\MailchimpList\CollectionFactory $listCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Plumrocket\Newsletterpopup\Helper\Adminhtml $adminhtmlHelper,
        \Plumrocket\Newsletterpopup\Model\IntegrationRepositoryInterface $integrationRepository,
        \Plumrocket\Newsletterpopup\Model\ResourceModel\MailchimpList\CollectionFactory $listCollectionFactory,
        array $data = []
    ) {
        $this->adminhtmlHelper = $adminhtmlHelper;
        $this->integrationRepository = $integrationRepository;
        $this->listCollectionFactory = $listCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return null|\Plumrocket\Newsletterpopup\Model\Popup
     */
    public function getPopup()
    {
        return $this->getParentBlock()->getPopup();
    }

    /**
     * @return array
     */
    public function getLists()
    {
        $result = [];
        $popup = $this->getPopup();

        if ($popup
            && ($popup->getId() > 0)
        ) {
            $result = $this->getGroupedSavedLists();

            foreach (array_keys($result) as $integrationId) {
                if ('mailchimp' == $integrationId) {
                    if (! $this->adminhtmlHelper->isMaichimpEnabled()
                        || ! $popup->getPreparedIntegrationEnable('mailchimp')
                    ) {
                        unset($result[$integrationId]);
                    }

                    continue;
                }

                if (! $this->integrationRepository->get($integrationId)->isEnable()) {
                    unset($result[$integrationId]);
                }
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getPopupIntegrationModes()
    {
        if ($popup = $this->getPopup()) {
            $result = $popup->getPreparedIntegrationMode();
            $enabled = $popup->getPreparedIntegrationEnable();

            if (! is_array($result)) {
                $result = [];
            }

            if ($this->adminhtmlHelper->isMaichimpEnabled()) {
                $result['mailchimp'] = (string)$popup->getData('subscription_mode');
            }

            foreach ($result as $integrationId => $value) {
                if (empty($enabled[$integrationId])) {
                    unset($result[$integrationId]);
                }
            }

            return $result;
        }

        return [];
    }

    /**
     * @return array
     */
    private function getGroupedSavedLists()
    {
        $result = [];
        $popup = $this->getPopup();

        if ($popup && $popup->getId()) {
            $integrationIds = [];
            $enableData = $popup->getPreparedIntegrationEnable();

            if (is_array($enableData) && ! empty($enableData)) {
                foreach ($enableData as $integrationId => $value) {
                    if (0 !== (int)$value) {
                        $integrationIds[] = $integrationId;
                    }
                }
            }

            if (! empty($integrationIds)) {
                /** @var \Plumrocket\Newsletterpopup\Model\ResourceModel\MailchimpList\Collection $collection */
                $collection = $this->listCollectionFactory->create();
                $collection->addIntegrationAndPopupFilter($integrationIds, $this->getPopup()->getId(), true);
                $result = $collection->getGroupedIntegrationLists();
            }
        }

        return $result;
    }

    /**
     * @return bool
     */
    public function canShow()
    {
        foreach ($this->getPopupIntegrationModes() as $mode) {
            if (in_array($mode, $this->allowedModes)) {
                return true;
            }
        }

        return false;
    }
}
