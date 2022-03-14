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
namespace FME\Mediaappearance\Model\Mediaappearance;

use FME\Mediaappearance\Model\ResourceModel\Mediaappearance\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
/**
 * Class DataProvider
 */
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \Magento\Cms\Model\ResourceModel\Block\Collection
     */
    protected $collection;
    public $storeManager;
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;
    protected $helper;
    /**
     * @var array
     */
    protected $loadedData;
    protected $pool;
    /**
     * Constructor
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $blockCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
       PoolInterface $pool,
        CollectionFactory $blockCollectionFactory,
        DataPersistorInterface $dataPersistor,
        \FME\Mediaappearance\Helper\Data $helper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $blockCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->helper = $helper;
     $this->pool = $pool;
        $this->storeManager=$storeManager;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    


    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {

      //  echo"asdas";exit;
      
        $baseurl =  $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
         // print_r($items);
        /** @var \Magento\Cms\Model\Block $block */
        foreach ($items as $block) {
            $data=$block->getData();
     //  print_r($block->getData());
         //exit;
           if(isset($data['category_ids']))
           {
            $data['mediagallery_categories'] = explode(',', $block->getData()['category_ids']);
           }
        //    $data['store_id']=array(
        //     0  => 0,
        //     1 => 1);
           $this->loadedData[$block->getId()] = $data;
             $temp = $block->getData();
            // print_r($temp);
            // exit;
             //$temp['filethumb'] = 'http://mag2-cms.fmeextensions.net/advancearticles//pub/media/tmp/articles/igallery/media//5/5/55b792bc7157f.jpg?1519024205704';
      
              /*  $img = [];
            if ($temp['filename']!='') {
                $img[0]['name'] = $temp['filename'];
                $img[0]['url'] = $baseurl.$temp['filename'];
                $temp['filename'] = $img;
            }

               $thumb = [];
                $thumb[0]['name'] = $temp['filethumb'];
            if (strpos($temp['filethumb'], 'https://') !== false) {
                $thumb[0]['url'] = $temp['filethumb'];
            } else {
                $thumb[0]['url'] = $baseurl.$temp['filethumb'];
            }
               //http://mag2-cms.fmeextensions.net/advancearticles//pub/media/tmp/articles/igallery/media//5/5/55b792bc7157f.jpg?1519024205704
               //print_r($thumb);
               $temp['filethumb']=$thumb;
               $temp['filethumb'][0]['url'] = $this->removeThumb($thumb[0]['url']);
               //print_r($temp['filethumb']);
               //$temp['filethumb'] = 'http://mag2-cms.fmeextensions.net/advancearticles//pub/media/tmp/articles/igallery/media//5/5/55b792bc7157f.jpg?1519024205704';
        
        */    }
        //check VIdeo Media
//print_r($this->loadedData);
  //      exit;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

//Load product by product id
        $model = $objectManager->create('FME\Mediaappearance\Model\Media');
           // print_r($this->loadedData);exit;
        if (!empty($this->loadedData)) {

        foreach ($this->pool->getModifiersInstances() as $modifier) {
           // if(count($model->getRelatedMediaVideo(1))>0){
            $this->loadedData = $modifier->modifyData($this->loadedData);
        //}
        }

    }
        $data = $this->dataPersistor->get('mediagallery');
      
        if (!empty($data)) {
            $block = $this->collection->getNewEmptyItem();
            $block->setData($data);
            $this->loadedData[$block->getId()] = $block->getData();
            
            $this->dataPersistor->clear('mediagallery');
        }
    //print_r($this->loadedData);exit;
        if (empty($this->loadedData)) {
            return $this->loadedData;
        } else {
            if ($block->getData('filename') != null || $block->getData('filethumb') != null) {
                $t2[$block->getId()] = $temp;
                // echo "t1";
                // print_r($t2);exit;
                return $t2;
            } else {
               // print_r($this->loadedData);exit;
                return $this->loadedData;
            }
        }
     
        

        /*foreach ($this->pool->getModifiersInstances() as $modifier) {
            $this->data = $modifier->modifyData($this->data);
        }

        return $this->data;*/


    }
    public function removeThumb($string)
    {
        if (strpos($string, 'thumb') !== false) {
            
        

        $param="thumb/";
        $pos = strpos($string, $param);
        $endpoint = $pos + strlen($param);
        $newStr1 = substr($string,0,$pos);
        $newStr2 = substr($string,$endpoint,strlen($string) );
        return $newStr1.$newStr2;
        }
        else
        return $string;
    }
   /* public function getData()
    {
        
        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $this->data = $modifier->modifyData($this->data);
        }
        return $this->data;
    }*/
    /**
     * {@inheritdoc}
     */
    public function getMeta()
    {
        $meta = parent::getMeta();
        
        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $meta = $modifier->modifyMeta($meta);
        }
        return $meta;
    }
}
