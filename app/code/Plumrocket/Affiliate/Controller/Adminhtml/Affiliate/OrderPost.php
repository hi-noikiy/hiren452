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
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Controller\Adminhtml\Affiliate;

class OrderPost extends \Plumrocket\Affiliate\Controller\Adminhtml\Affiliate
{
    /**
     * Order Info Factory
     * @var \Plumrocket\Affiliate\Model\Order\InfoFactory
     */
    protected $infoFactory;

    /**
     * OrderPost constructor.
     *
     * @param \Magento\Backend\App\Action\Context           $context
     * @param \Plumrocket\Affiliate\Model\AffiliateManager  $affiliateManager
     * @param \Plumrocket\Affiliate\Model\TypeFactory       $typeFactory
     * @param \Plumrocket\Affiliate\Model\Order\InfoFactory $infoFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Plumrocket\Affiliate\Model\AffiliateManager $affiliateManager,
        \Plumrocket\Affiliate\Model\TypeFactory $typeFactory,
        \Plumrocket\Affiliate\Model\Order\InfoFactory $infoFactory
    ) {
        parent::__construct($context, $affiliateManager, $typeFactory);
        $this->infoFactory = $infoFactory;
    }

    /**
     * Save affiliate data in order
     */
    public function execute()
    {
        $_data = $this->getRequest()->getPost('affiliate');
        $data = [];
        foreach (['order_id', 'affiliate_id', 'comment'] as $key) {
            if (! isset($_data[$key])) {
                $this->messageManager->addError(__('%1 is missing.', $key));
                $this->_redirect($this->_redirect->getRefererUrl());
                return;
            }
            $data[$key] = $_data[$key];
        }

        $info = $this->infoFactory->create()
            ->load($data['order_id'], 'order_id')
            ->addData($data)
            ->save();

        $this->messageManager->addSuccess(__('Data saved successfully.'));
        $this->_redirect($this->_redirect->getRefererUrl());
    }


}
