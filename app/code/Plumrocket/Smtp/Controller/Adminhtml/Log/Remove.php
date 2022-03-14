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
 * @package     Plumrocket SMTP
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Smtp\Controller\Adminhtml\Log;

use Exception;

class Remove extends \Magento\Backend\App\Action
{
    /**
     * @var \Plumrocket\Smtp\Model\LogFactory
     */
    private $logFactory;

    /**
     * Remove constructor.
     *
     * @param \Plumrocket\Smtp\Model\ResourceModel\Log\CollectionFactory $logFactory
     * @param \Magento\Backend\App\Action\Context                        $context
     */
    public function __construct(
        \Plumrocket\Smtp\Model\ResourceModel\Log\CollectionFactory $logFactory,
        \Magento\Backend\App\Action\Context $context
    ) {
        parent::__construct($context);

        $this->logFactory = $logFactory;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        try {
            $this->logFactory->create()->walk('delete');
            $this->messageManager->addSuccessMessage(__('All the logs have been removed.'));
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong.'));
        }

        return  $this->resultRedirectFactory->create()
            ->setPath('*/*/index');
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Plumrocket_Smtp::config_prsmtp_clearlog');
    }
}
