<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Aumika\Military\Controller\Index;

/**
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Download extends \Magento\Framework\App\Action\Action implements \Magento\Framework\App\Action\HttpGetActionInterface
{
    public function __construct(
        \Magento\Framework\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\Filesystem\Driver\File $driverFile,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->directoryList =$directoryList;
        $this->driverFile = $driverFile;
        $this->fileFactory = $fileFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $encodename = $this->getRequest()->getParam('encodename', false);
        if (!$encodename) {
            $fileName = $this->getRequest()->getParam('filename', false);
        } else {
            $fileName = base64_decode($encodename);
        }
        return $this->fileFactory->create(
            $fileName,
            [
                'type' => 'filename',
                'value' => 'militarycustomer/' . $fileName,
            ],
            \Magento\Framework\App\Filesystem\DirectoryList::MEDIA
        );
    }
}
