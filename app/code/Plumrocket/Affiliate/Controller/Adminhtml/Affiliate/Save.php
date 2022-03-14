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

class Save extends \Plumrocket\Affiliate\Controller\Adminhtml\Affiliate
{
    const FILE_EXTENSION_FAIL = 12343;

    /**
     * Date for setUpdatedAt() and setCreatedAt()
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    public $dateTime;

    /**
     * Filesystem Factory
     * @var \Magento\Framework\FilesystemFactory
     */
    public $filesystemFactory;

    /**
     * File Uploader Factory
     * @var \Magento\MediaStorage\Model\File\UploaderFactoryFactory
     */
    public $uploaderFactory;

    /**
     * Save constructor.
     *
     * @param \Magento\Backend\App\Action\Context                     $context
     * @param \Plumrocket\Affiliate\Model\AffiliateManager            $affiliateManager
     * @param \Plumrocket\Affiliate\Model\TypeFactory                 $typeFactory
     * @param \Magento\Framework\FilesystemFactory                    $filesystemFactory
     * @param \Magento\Framework\Stdlib\DateTime\DateTime             $dateTime
     * @param \Magento\MediaStorage\Model\File\UploaderFactoryFactory $uploaderFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Plumrocket\Affiliate\Model\AffiliateManager $affiliateManager,
        \Plumrocket\Affiliate\Model\TypeFactory $typeFactory,
        \Magento\Framework\FilesystemFactory $filesystemFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\MediaStorage\Model\File\UploaderFactoryFactory $uploaderFactory
    ) {
        parent::__construct($context, $affiliateManager, $typeFactory);
        $this->dateTime             = $dateTime;
        $this->filesystemFactory    = $filesystemFactory;
        $this->uploaderFactory      = $uploaderFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function _saveAction()
    {
        $request = $this->getRequest();
        if (!$request->isPost()) {
            $this->getResponse()->setRedirect($this->getUrl('*/*'));
        }
        $model = $this->_getModel();

        try {

            $data = $request->getParams();
            $date = $this->dateTime->gmtDate();

            if (count($this->getRequest()->getFiles())) {
                $aMediaDName = \Plumrocket\Affiliate\Helper\Data::$routeName;
                $mediaDirectory = $this->filesystemFactory->create()
                    ->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);

                foreach($model->getPageSections() as $section){
                    $fileLable = 'section_'.$section['key'].'_library';
                    try {
                        $uploader = $this->uploaderFactory->create();
                        $uploader = $uploader->create(['fileId' => $fileLable]);
                        $uploader->setAllowedExtensions(['js']);
                        $uploader->setAllowRenameFiles(false);
                        $uploader->setFilesDispersion(true);
                        $uploader->setAllowCreateFolders(true);
                        $result = $uploader->save(
                            $mediaDirectory->getAbsolutePath($aMediaDName)
                        );
                        $data[$fileLable] = $aMediaDName . $result['file'];
                    } catch (\Exception $e) {
                        if ($e->getCode() != \Magento\Framework\File\Uploader::TMP_NAME_EMPTY) {
                            throw new \Exception($e->getMessage());
                        }
                    }
                }
            }

            foreach($model->getPageSections() as $section){
                $fileDeleteLable = 'section_'.$section['key'].'_library_delete';
                $fileLable = 'section_'.$section['key'].'_library';

                if (isset($data[$fileDeleteLable]) && $model->getData($fileLable)){
                    $data[$fileLable] = '';
                    $mediaDirectory->delete($model->getData($fileLable));
                }
            }

            if ($request->getParam('additional_data')) {
                $model->setAdditionalDataValues($request->getParam('additional_data'));
                unset($data['additional_data']);

            }

            if ($request->getParam('stores')) {
                $model->setStores($request->getParam('stores'));
                unset($data['stores']);
            }

            $model->addData($data)
                ->setUpdatedAt($date);

            if (!$model->getId()) {
                $model->setCreatedAt($date);
            }

            $model->save();

            $this->messageManager->addSuccess(__($this->_objectTitle.' has been saved.'));
            $this->_setFormData(false);

            if ($request->getParam('back')) {
                $this->_redirect('*/*/edit', [$this->_idKey => $model->getId()]);
            } else {
                $this->_redirect('*/*');
            }
            return;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError(nl2br($e->getMessage()));
            $this->_setFormData();
        } catch (\Exception $e) {
            if ($e->getCode() == self::FILE_EXTENSION_FAIL) {
                $this->messageManager->addException($e, $e->getMessage());
            } else {
                $this->messageManager->addException($e, __('Something went wrong while saving this '.strtolower($this->_objectTitle)));
            }
            $this->_setFormData();
        }

        $this->_forward('edit');
    }


}
