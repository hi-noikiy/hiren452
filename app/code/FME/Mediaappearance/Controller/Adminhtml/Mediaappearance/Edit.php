<?php

/**
 * FME Extensions
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the fmeextensions.com license that is
 * available through the world-wide-web at this URL:
 * https://www.fmeextensions.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  FME
 * @author     Atta <support@fmeextensions.com>
 * @package   FME_Mediaappearance
 * @copyright Copyright (c) 2019 FME (http://fmeextensions.com/)
 * @license   https://fmeextensions.com/LICENSE.txt
 */
namespace FME\Mediaappearance\Controller\Adminhtml\Mediaappearance;

class Edit extends \FME\Mediaappearance\Controller\Adminhtml\Mediaappearance
{

    public function execute()
    {
       // echo "asdasdas";exit;
        $resultPage = $this->resultPageFactory->create();
        $id = $this->getRequest()->getParam('id');
        $model = $this->_objectManager->create('FME\Mediaappearance\Model\Mediaappearance')->load($id);
        $photogallery = $this->_objectManager->create('FME\Mediaappearance\Model\MediaFactory');
        $collection = $photogallery->create()->getCollection()
        ->addFieldToFilter('mediagallery_id', $id)
        //->addFieldToFilter('status', 1)
        ->addFieldToFilter('mediatype', 3);
        

        //echo "czollection";
        //print_r($collection->getData());
        //exit;
        if ($model->getId() || $id == 0) {
            
            $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
            //print_r($model->getData()); //exit;
            
           $this->_objectManager->get('Magento\Framework\Registry')->register('mediagallery_data', $model);
           $this->_objectManager->get('Magento\Framework\Registry')->register('mediagallery_img', $collection);
            
          // $this->_coreRegistry->register('mediagallery_img', $collection);

            $resultPage = $this->_initAction();
            $resultPage->addBreadcrumb(
                $id ? __('Edit Media') : __('New Media'),
                $id ? __('Edit Media') : __('New Media')
            );
            $resultPage->getConfig()->getTitle()->prepend(__('Media Gallery'));
            $resultPage->getConfig()->getTitle()
                    ->prepend($model->getMediagalleryId() ? $model->getGalName() : __('New Media Gallery'));
            return $resultPage;
        } else {
            $this->messageManager->addError(__('File does not exist'));
            $this->_redirect('*/*/');
        }
    }
}
