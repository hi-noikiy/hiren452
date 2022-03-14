<?php

namespace Meetanshi\Inquiry\Controller\Adminhtml\Inquiry;

use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Meetanshi\Inquiry\Model\InquiryFactory;
use Magento\Store\Model\StoreManagerInterface;
use Meetanshi\Inquiry\Helper\Data;
use Magento\Framework\Exception\LocalizedException;

class Save extends Action
{
    protected $gridFactory;
    protected $fileUploaderFactory;
    protected $filesystem;
    protected $Files;
    protected $storeManager;
    protected $helper;

    public function __construct(
        Context $context,
        UploaderFactory $fileUploaderFactory,
        Filesystem $filesystem,
        InquiryFactory $gridFactory,
        Data $helper,
        StoreManagerInterface $storeManager
    ) {
    
        parent::__construct($context);
        $this->fileUploaderFactory = $fileUploaderFactory;
        $this->filesystem = $filesystem;
        $this->gridFactory = $gridFactory;
        $this->helper = $helper;
        $this->storeManager = $storeManager;
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $rowData = $this->gridFactory->create();
        $allowedType = $this->helper->getAllowedFileTypes();
        $allowedType = explode(",", $allowedType);
        $filename = "";

        try {
            if ($this->getRequest()->getFiles('files')['name'] && file_exists($this->getRequest()->getFiles('files')['tmp_name'])) {
                    $uploader = $this->fileUploaderFactory->create(['fileId' => 'files']);
                    $uploader->setAllowedExtensions($allowedType);
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(false);
                    $path = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('inquiry/');
                    $result = $uploader->save($path);
                    $filename .= $result['file'];
                    $data['files'] = 'inquiry/'.$filename;
            } else {
                if (isset($data['files']) && isset($data['files']['value'])) {
                    if (isset($data['files']['delete'])) {
                        $data['files'] = '';
                        $data['delete_image'] = true;
                    } elseif (isset($data['files']['value'])) {
                        $data['files'] = $data['files']['value'];
                    } else {
                        $data['files'] = '';
                    }
                }else{
                    $data['files'] = '';
                }
            }
        }
        catch (\Exception $e){
            throw new LocalizedException($e->getMessage());
        }

        if (is_array($data['store_view'])){
            $field = implode(',',$data['store_view']);
            $data['store_view'] = $field;
        }

        $flg = false;
        $rowData->setData($data);
        if (isset($data['dealer_id'])) {
            $rowData->setId($data['dealer_id']);
        }

        try {
            $rowData->save();
            if ($flg) {
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('meetanshi_inquiry/inquiry/createcustomer', ['id' => $rowData->getId()]);
                return $resultRedirect;
            }
        } catch (\Exception $e) {
            throw new LocalizedException($e->getMessage());
        }
        $this->messageManager->addSuccess(__('Inquiry Detailed Saved.'));
        $this->_redirect('meetanshi_inquiry/inquiry/index');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Meetanshi_Inquiry::inquiry');
    }
}
