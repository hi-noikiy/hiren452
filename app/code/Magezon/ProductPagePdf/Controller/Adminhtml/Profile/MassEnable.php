<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://www.magezon.com/license
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_ProductPagePdf
 * @copyright Copyright (C) 2020 Magezon (https://www.magezon.com)
 */

namespace Magezon\ProductPagePdf\Controller\Adminhtml\Profile;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Ui\Component\MassAction\Filter;
use Magezon\ProductPagePdf\Model\Profile;
use Magezon\ProductPagePdf\Model\ResourceModel\Profile\CollectionFactory as ProfileCollectionFactory;

class MassEnable extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magezon_ProductPagePdf::profile';

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var ProfileCollectionFactory
     */
    protected $profileCollectionFactory;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param ProfileCollectionFactory $fileCollectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        ProfileCollectionFactory $profileCollectionFactory
    ) {
        $this->filter = $filter;
        $this->profileCollectionFactory = $profileCollectionFactory;
        parent::__construct($context);
    }

    /**
     * @return Redirect|ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $fileCollection = $this->profileCollectionFactory->create();
        $collection = $this->filter->getCollection($fileCollection);
        $collectionSize = $collection->getSize();

        foreach ($collection as $profile) {
            $profile->setIsActive(Profile::STATUS_ENABLED)->save();
        }

        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been enabled.', $collectionSize));

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
