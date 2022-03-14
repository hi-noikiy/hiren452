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
namespace FME\Mediaappearance\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

class Thumbnail extends \Magento\Ui\Component\Listing\Columns\Column
{

    const NAME = 'thumbnail';
    const ALT_FIELD = 'name';

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->urlBuilder = $urlBuilder;
        $this->_storeManager = $storeManager;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return void
     */
    public function prepareDataSource(array $dataSource)
    {

      
        $media_url = $this->_storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        );
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');

            foreach ($dataSource['data']['items'] as & $item) {
                if ($item['filethumb'] == null) {
                    //$media_url = $this->urlBuilder->getBaseUrl(). 'app/code/FME/Mediaappearance/adminhtml/web/images/';
                    $item['filethumb'] = 'mediaappearance/video_icon_full.jpg';
                }

                if (strpos($item['filethumb'], 'https://') !== false) {
                    //echo"<br> hhtpsss<br>";
                   // print_r($item['filethumb']);

                    $item[$fieldName . '_src'] = $item['filethumb'];
                    $item[$fieldName . '_orig_src'] =$item['filethumb'];
                } else {
                    //echo"<br> hhtpsss1<br>";
                   // print_r($item['filethumb']);
                    $item['filethumb']=$this->removeThumb($item['filethumb']);
                     $item[$fieldName . '_src'] = $media_url . $item['filethumb'];
                     $item[$fieldName . '_orig_src'] = $media_url . $item['filethumb'];
                }

                
                $item[$fieldName . '_alt'] = $item['title'];
                $item[$fieldName . '_link'] = $this->urlBuilder->getUrl(
                    'mediaappearanceadmin/mediaappearance/edit',
                    ['id' => $item['mediaappearance_id'], 'store' => $this->context->getRequestParam('store')]
                );
            }
        }
        //print_r($dataSource);
        return $dataSource;
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
}
