<?php

namespace Aumika\Military\Controller\Index;

use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;

class Fileupload extends \Magento\Framework\App\Action\Action implements HttpPostActionInterface
{
   const ARTWORK_PATH = "militarycustomer";

    protected $jsonHelper;
    protected $resultJsonFactory;
    protected $mediaDirectory;
    protected $fileUploaderFactory;
    protected $storeManager;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->jsonHelper = $jsonHelper;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->fileUploaderFactory = $fileUploaderFactory;
        $this->storeManager = $storeManager;
    }

    public function execute()
    {
        $data = [];
        $message = "";
        $error = false;
        try {
            $files = $this->getRequest()->getFiles();

            $target = $this->mediaDirectory->getAbsolutePath(self::ARTWORK_PATH . '/');
            if ($files['pl_ajax_file_upload']) {
                $uploader = $this->fileUploaderFactory->create(['fileId' => 'pl_ajax_file_upload']);
            } 
            $fileType = $uploader->getFileExtension();
            $newFileName = uniqid(random_int(0, 10), true) . time() . '.' . $fileType;
            $uploader->setAllowedExtensions(["pdf", "jpg", "png", "jpeg"]);
            $uploader->setAllowRenameFiles(true);
            $uploader->setAllowCreateFolders(true);
            $result = $uploader->save($target, $newFileName);
            if ($result['file']) {
                $error = false;
                $message = "File has been successfully uploaded";
                $data = [
                    'filename' => '/' . $result['file'],
                    'path' => $this->storeManager->getStore()->getUrl('military/index/download/', ['filename' => $result['file']]),
                    'fileType' => $fileType,
                ];
            }
        } catch (\Exception $e) {
            $error = true;
            $message = $e->getMessage();
        }
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData([
            'message' => $message,
            'data' => $data,
            'error' => $error
        ]);
    }
}
