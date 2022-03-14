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

use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use FME\Prodfaqs\Model\Faqs;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\TestFramework\Inspection\Exception;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\RequestInterface;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     *
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $dataPersistor;
    protected $scopeConfig;
   
    protected $_escaper;
    protected $inlineTranslation;
    protected $_dateFactory;
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $coreresource,
        Context $context,
        DataPersistorInterface $dataPersistor,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Stdlib\DateTime\DateTimeFactory $dateFactory
    ) {

        $this->dataPersistor = $dataPersistor;
         $this->scopeConfig = $scopeConfig;
         $this->_escaper = $escaper;
        $this->_dateFactory = $dateFactory;
         $this->inlineTranslation = $inlineTranslation;
        parent::__construct($context);
        $this->_coreresource = $coreresource;
    }

    public function execute()
    {

        $data = $this->getRequest()->getPostValue();
       //print_r($data);exit;
       //exit;

        //for Images 
        $gallery = isset($data['gallery']) ? $data['gallery'] : [];
        $_photos_info_images = isset($gallery['images']) ? $gallery['images'] : [];
        //End Images Variables
        if(isset($data['data']))
        {
            $mediadata=$data['data']['product']['attachments'];
            $_photos_info=$mediadata['dynamic_rows'];
        }
        else{
            $_photos_info=[];
        }
        if ($data = $this->getRequest()->getPostValue()) {
            if (isset($data['filename'][0]['name']) && isset($data['filename'][0]['tmp_name'])) {
                $data['filename'] ='/mediaappearance/files/'.$data['filename'][0]['name'];
            } elseif (isset($data['filename'][0]['name']) && !isset($data['image'][0]['tmp_name'])) {
                $data['filename'] =$data['filename'][0]['name'];
            } else {
                $data['filename'] = null;
            }
            if (isset($data["category_products"])) {
                $cat_array = json_decode($data['category_products'], true);


             
                $pro_array = array_values($cat_array);
                $c=0;
                foreach ($cat_array as $key => $value) {
                    $pro_array[$c] = $key;
                    $c++;
                }

                unset($data['category_products']);
                $data['product_id'] = $pro_array;
            }
            $id = $this->getRequest()->getParam('mediagallery_id');
            if (empty($data['mediagallery_id'])) {
                $data['mediagallery_id'] = null;
            }
            $model = $this->_objectManager->create('FME\Mediaappearance\Model\Mediaappearance');
           
            if ($id) {
                //echo $id; exit;
                $model->load($id);
            }
          
            $model->setData($data);

            if ($id) {
                $model->setId($id);
            }

            $this->inlineTranslation->suspend();
            try {
                if ($model->getCreatedTime() == null || $model->getUpdateTime() == null) {
                    $model->setCreatedTime(date('y-m-d h:i:s'))
                            ->setUpdateTime(date('y-m-d h:i:s'));
                } else {
                    $model->setUpdateTime(date('y-m-d h:i:s'));
                }

                $model->save();
                $_conn_read = $this->_coreresource->getConnection('core_read');
                $_conn = $this->_coreresource->getConnection('core_write');
                $photogallery_images_table = $this->_coreresource->getTableName('fme_mediaappearance');
               
               //Media Videos
                if (empty($data['mediagallery_id'])) {
                    foreach ($_photos_info as $_photo_info) {
                        if (!isset($_photo_info['mediagallery_id'])) {
                            if (empty($_lookup)) {
                               // exit;
                               $thumb="";
                                $mediatype=0;
                                $url="";
                                $filename="";
                                 if($_photo_info['type']=='url'){
                                    $mediatype=1;
                                    $url=$_photo_info['link_url'];
                                   
                                }else{
                                    $mediatype=2;
                                    $filename=$_photo_info['filename'][0]['file'];
                                }
                                // if($_photo_info['link_url']!=null ){
                                //     $mediatype=1;
                                //     $url=$_photo_info['link_url'];
                                   
                                // }else{
                                //     $mediatype=2;
                                //     $filename=$_photo_info['filename'][0]['file'];
                                // }
                                if(isset($_photo_info['filethumb'][0]['file']))
                                {
                                    $thumb=$_photo_info['filethumb'][0]['file'];
                                }else{
                                    $thumb=$_photo_info['filethumb'][0]['name'];
                                }

                                $_conn->insert(
                                    $photogallery_images_table,
                                    [
                                       // 'filethumb' => $_photo_info['filethumb'][0]['file'],
                                       'filethumb' => $thumb,
                                       'media_title' => $_photo_info['title'],
                                        'mediatype' =>$mediatype,
                                        'filename' =>$filename,
                                        'videourl' =>$url,
                                        'status' =>(int)$_photo_info['status'],
                                        'featured_media' =>$_photo_info['featured'] ,
                                        'mediagallery_id' => $model->getId(),
                                    
                                    ]
                                );
                            }
                        }
                    }
                }
                //Insert Media VIdeos Ends


                //Insert Images
                if (empty($data['mediagallery_id'])) {
                    if (!empty($_photos_info_images)) {
                        foreach ($_photos_info_images as $_photo_info_images) {
                            //Do update if we have gallery id (menaing photo is already saved)
                            if (!$_photo_info_images['mediagallery_id']) {
                            
                                //exit;
                                $_lookup = $_conn_read->fetchAll(
                                    "SELECT * FROM " . $photogallery_images_table . " WHERE filethumb = ?",
                                    $_photo_info_images['file']
                                );
                                //echo "here2"; exit;
                                if (empty($_lookup)) {
                                    $_conn->insert(
                                        $photogallery_images_table,
                                        [
                                        'filethumb' => str_replace(".tmp", "", $_photo_info_images['file']),
                                        'media_title' => $_photo_info_images['label'],
                                        'mediatype' => 3,
                                        'mediagallery_id' => $model->getId(),
                                        'status' => 1,

                                        ]
                                    );
                                }
                            }
                        }
                    }
                }   
                //Images ENds





                $this->messageManager->addSuccess(__('Media was successfully saved'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['id' => $model->getId()]);
                    return;
                }

                $this->_redirect('*/*/');
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData($data);
                $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
                return;
            }
        }
        $this->messageManager->addError(__('Unable to find Media to save'));
        $this->_redirect('*/*/');
    }

    

    protected function _isAllowed()
    {
        return true;
    }
}