<?php
/**
 *
 * Copyright © 2013-2018 commercepundit, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace BT\News\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;

/**
 * Class Save
 * @package BT\News\Controller\Adminhtml\Index
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 */
class Save extends \Magento\Backend\App\Action //implements HttpPostActionInterface
{
    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $cacheTypeList;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $_directory;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $session;

    /**
     * @var \CP\Careers\Model\Careers
     */
    protected $careers;

    protected $_imageuploader;

    protected $_iconuploader;

    /**
     * Save constructor.
     * @param Action\Context $context
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \CP\Careers\Model\Careers $careers
     * @param \Magento\Backend\Model\Session $session
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \BT\News\Model\ImageUploader $imageuploader,
        \BT\News\Model\NewsFactory $newsFactory,
        \Magento\Backend\Model\Session $session
    ) {
        $this->session = $session;
        $this->newsFactory = $newsFactory;
        $this->_imageuploader = $imageuploader;
        $this->cacheTypeList = $cacheTypeList;
        parent::__construct($context);
    }

    /**
     * @return $this
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $data['customergroup'] = (!empty($data['customergroup'])) ? implode(",", $data['customergroup']) : '';
        $imagefile = $this->getRequest()->getFiles('imageupload');
        //$iconfile = $this->getRequest()->getFiles('iconupdate');
        if (!empty($imagefile['name'])) {
            $imageuploadPath =  $this->_imageuploader->saveFileToTmpDir($imagefile);
            $data['imageupload'] = $imageuploadPath['url'];
        }
        // if (!empty($iconfile['name'])) {
        //     $iconuploadPath =  $this->_iconuploader->saveFileToTmpDir($iconfile);
        //     $data['iconupdate'] = $iconuploadPath['url'];
        // }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $model = $this->newsFactory->create();
            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $model->load($id);
            }
            $data = $this->setImageData($data);
            $model->setData($data);
            try {
                $model->save();
                $this->cacheTypeList->invalidate('full_page');
                $this->messageManager->addSuccess(__('You saved this news.'));
                $this->session->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the Jobs.'));
            }
            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    private function setImageData(array $data)
    {
        $images = ["imageupload"];
        foreach ($images as $image) {
            if (!isset($data[$image]['delete'])) {
                if (isset($data[$image]['value'])) {
                    $data[$image] = $data[$image]['value'];
                }
            } else {
                $data[$image] = '';
            }
        }
        return $data;
    }
}
