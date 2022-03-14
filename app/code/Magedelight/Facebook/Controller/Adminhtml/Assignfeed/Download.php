<?php
/**
 * Magedelight
 * Copyright (C) 2019 Magedelight <info@magedelight.com>
 *
 * @category Magedelight
 * @package Magedelight_Facebook
 * @copyright Copyright (c) 2019 Mage Delight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 */
namespace Magedelight\Facebook\Controller\Adminhtml\Assignfeed;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Filesystem\Directory\ReadFactory;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\Result\RawFactory;

class Download extends \Magento\Backend\App\Action
{
    const MAGEDELIGHT_FACEBOOK = 'Magedelight_Facebook';
    const FILE_NAME = 'md_product_mapping.csv';
    
    /**
     * 
     * @param Context $context
     * @param ComponentRegistrar $componentRegistrar
     * @param ReadFactory $readFactory
     * @param FileFactory $fileFactory
     * @param RawFactory $resultRawFactory
     */
    public function __construct(
        Context $context,
        ComponentRegistrar $componentRegistrar,
        ReadFactory $readFactory,
        FileFactory $fileFactory,
        RawFactory $resultRawFactory    
       ) {
        $this->componentRegistrar = $componentRegistrar;
        $this->readFactory = $readFactory;
        $this->fileFactory = $fileFactory;
        $this->resultRawFactory = $resultRawFactory;
        parent::__construct($context);
    }
    
    public function execute()
    {
        $fileName = self::FILE_NAME;
        $moduleDir = $this->componentRegistrar->getPath(ComponentRegistrar::MODULE, self::MAGEDELIGHT_FACEBOOK);
        $fileAbsolutePath = $moduleDir . '/Files/Sample/' . $fileName;
        $directoryRead = $this->readFactory->create($moduleDir);
        $filePath = $directoryRead->getRelativePath($fileAbsolutePath);
        if (!$directoryRead->isFile($filePath)) {
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $this->messageManager->addError(__('There is no sample file for this entity.'));
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*/importrequest');
            return $resultRedirect;
        }

        $fileSize = isset($directoryRead->stat($filePath)['size'])
            ? $directoryRead->stat($filePath)['size'] : null;

        $this->fileFactory->create(
            $fileName,
            null,
            DirectoryList::VAR_DIR,
            'application/octet-stream',
            $fileSize
        );
       
        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();
        $resultRaw->setContents($directoryRead->readFile($filePath));
        return $resultRaw;
    }
    
    protected function _isAllowed()
    {
        return true;
    }
}
