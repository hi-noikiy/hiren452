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
namespace FME\Mediaappearance\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\UrlInterface;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Ui\Component\Form\Element\DataType\Number;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Element\Select;
use Magento\Ui\Component\Form\Element\MultiSelect;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\DynamicRows;
use Magento\Ui\Component\Form;
use FME\Mediaappearance\Model\Config\Source\TypeUpload;
use FME\Mediaappearance\Model\Config\Source\Cmspages;

class CompositeAttachments extends AbstractModifier
{
 
    // Components indexes
    const CUSTOM_FIELDSET_INDEX = 'custom_fieldset';
    const CUSTOM_FIELDSET_CONTENT = 'custom_fieldset_content';
    const CONTAINER_HEADER_NAME = 'dynamic_rows';
 
    // Fields names
   
 
    /**
     * @var \Magento\Catalog\Model\Locator\LocatorInterface
     */
    protected $locator;
    protected $typeUpload;
    protected $cgroups;
    protected $request;
    /**
     * @var ArrayManager
     */
    protected $arrayManager;
 
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;
    protected $_productattachments;
    /**
     * @var array
     */
    protected $meta = [];
 
    /**
     * @param LocatorInterface $locator
     * @param ArrayManager $arrayManager
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        LocatorInterface $locator,
        ArrayManager $arrayManager,
        TypeUpload $typeUpload,
        Cmspages $cgroups,
        \Magento\Framework\App\RequestInterface $request,
        \FME\Mediaappearance\Model\Media $productattachments,
        UrlInterface $urlBuilder
    ) {
        $this->locator = $locator;
        $this->arrayManager = $arrayManager;
        $this->typeUpload = $typeUpload;
        $this->cgroups = $cgroups;
        $this->request = $request;
        $this->_productattachments = $productattachments;
        $this->urlBuilder = $urlBuilder;
    }
 
    /**
     * Data modifier, does nothing in our example.
     *
     * @param array $data
     * @return array
     */
    public function modifyData(array $data)
    {
        // echo "<pre>";
     //  print_r($data);
        $id   = $this->request->getParam('id');
        if ($id!='') {
             $attachment = $this->_productattachments->getRelatedMediaVideoForModyfier($id);
       // print_r($attachment);//exit;
       

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

//Load product by product id
    $helper = $objectManager->create('FME\Mediaappearance\Helper\Data');
    $baseurl=$helper->getMediaUrl() . "mediaappearance/files/";
     // echo "<pre>";
            foreach ($data as &$dat) {
                foreach ($attachment as $key => $value) {
                    $dat['data']['product']['attachments']['dynamic_rows'][$key]['mediagallery_id'] = $value['mediagallery_id'];
                    $dat['data']['product']['attachments']['dynamic_rows'][$key]['record_id'] = $key;
                    $dat['data']['product']['attachments']['dynamic_rows'][$key]['title'] = $value['media_title'];
                    $dat['data']['product']['attachments']['dynamic_rows'][$key]['status'] = $value['status'];
                    $dat['data']['product']['attachments']['dynamic_rows'][$key]['title'] = $value['media_title'];
                    $dat['data']['product']['attachments']['dynamic_rows'][$key]['featured'] = $value['featured_media'];
                   // $dat['data']['product']['attachments']['dynamic_rows'][$key]['filethumb'] = 'https://cdn.pixabay.com/photo/2016/10/27/22/53/heart-1776746_960_720.jpg';

                    //filethumb
                    
                    if ($value['filethumb'] != '') {

                        $img=$value['filethumb'];
                        if (strpos($img, 'https://') !== false) {
                            $dat['data']['product']['attachments']['dynamic_rows'][$key]['filethumb'][0]['file'] = $value['filethumb'];
                            $dat['data']['product']['attachments']['dynamic_rows'][$key]['filethumb'][0]['name'] = $value['filethumb'];
                          //  $sizearr=getimagesize($baseurl.$value['filethumb']);
                           // $sizetotal=(int)$sizearr[0]*(int)$sizearr[1];
                           // $dat['data']['product']['attachments']['dynamic_rows'][$key]['filethumb'][0]['size'] =  $sizetotal;
                            $dat['data']['product']['attachments']['dynamic_rows'][$key]['filethumb'][0]['url'] =$value['filethumb'];
                            $dat['data']['product']['attachments']['dynamic_rows'][$key]['filethumb'][0]['status'] = 'old';


                        }
                        else{
                         $dat['data']['product']['attachments']['dynamic_rows'][$key]['filethumb'][0]['file'] = $value['filethumb'];
                        $dat['data']['product']['attachments']['dynamic_rows'][$key]['filethumb'][0]['name'] = $value['filethumb'];
                      //  $sizearr=getimagesize($baseurl.$value['filethumb']);
                       // $sizetotal=(int)$sizearr[0]*(int)$sizearr[1];
                       // $dat['data']['product']['attachments']['dynamic_rows'][$key]['filethumb'][0]['size'] =  $sizetotal;
                        $dat['data']['product']['attachments']['dynamic_rows'][$key]['filethumb'][0]['url'] =$baseurl.$value['filethumb'];
                        $dat['data']['product']['attachments']['dynamic_rows'][$key]['filethumb'][0]['status'] = 'old';
                        }
                    }
                    if ($value['filename'] == '') {
                        $dat['data']['product']['attachments']['dynamic_rows'][$key]['type'] = 'url';
                        $dat['data']['product']['attachments']['dynamic_rows'][$key]['link_url'] = $value['videourl'];
                    } else {
                        $dat['data']['product']['attachments']['dynamic_rows'][$key]['type'] = 'file';
                        $dat['data']['product']['attachments']['dynamic_rows'][$key]['filename'][0]['file'] = $value['filename'];
                        $dat['data']['product']['attachments']['dynamic_rows'][$key]['filename'][0]['name'] = $value['filename'];
                        $dat['data']['product']['attachments']['dynamic_rows'][$key]['filename'][0]['status'] = 'old';
                    }
                }
            }
        }else{
            $data=null;  
        }
       
       //  echo "<pre>";
      //  echo $id;
      //  print_r($this->locator->getProduct());
    //print_r($attachment);
      //print_r($data);
    // exit;
        return $data;
    }
 
    /**
     * Meta-data modifier: adds ours fieldset
     *
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta)
    {
        $this->meta = $meta;
        $this->addCustomFieldset();
 
        return $this->meta;
    }
 
    /**
     * Merge existing meta-data with our meta-data (do not overwrite it!)
     *
     * @return void
     */
    protected function addCustomFieldset()
    {
        $this->meta = array_merge_recursive(
            $this->meta,
            [
                static::CUSTOM_FIELDSET_INDEX => $this->getFieldsetConfig(),
            ]
        );
    }
 
    /**
     * Declare ours fieldset config
     *
     * @return array
     */
    protected function getFieldsetConfig()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Videos'),
                        'componentType' => Fieldset::NAME,
                        'dataScope' => static::DATA_SCOPE_PRODUCT, // save data in the product data
                        'provider' => static::DATA_SCOPE_PRODUCT . '_data_source',
                        'ns' => static::FORM_NAME,
                        'collapsible' => true,
                        'sortOrder' => 23,
                        'opened' => false,
                    ],
                ],
            ],
            'children' => [
                static::CONTAINER_HEADER_NAME => $this->getDynamicRows(),
            ],
        ];
    }
 

    protected function getDynamicRows()
    {
        $dynamicRows['arguments']['data']['config'] = [
            'addButtonLabel' => __('Add Video'),
            'componentType' => DynamicRows::NAME,
            'itemTemplate' => 'record',
            'renderDefaultRecord' => false,
            'columnsHeader' => true,
            'additionalClasses' => 'admin__field-wide',
            'dataScope' => 'attachments',
            'deleteProperty' => 'is_delete',
            'deleteValue' => '1',
        ];

        return $this->arrayManager->set('children/record', $dynamicRows, $this->getRecord());
    }

    /**
     * @return array
     */
    protected function getRecord()
    {
        $record['arguments']['data']['config'] = [
            'componentType' => Container::NAME,
            'isTemplate' => true,
            'is_collection' => true,
            'component' => 'Magento_Ui/js/dynamic-rows/record',
            'dataScope' => '',
        ];
        $recordPosition['arguments']['data']['config'] = [
            'componentType' => Form\Field::NAME,
            'formElement' => Form\Element\Input::NAME,
            'dataType' => Form\Element\DataType\Number::NAME,
            'dataScope' => 'sort_order',
            'visible' => false,
        ];
        $recordActionDelete['arguments']['data']['config'] = [
            'label' => null,
            'componentType' => 'actionDelete',
            'fit' => true,
        ];

        return $this->arrayManager->set(
            'children',
            $record,
            [
               // 'video_id' => $this->getTitleColumnhidden2(),
                //'youtube_thumb' => $this->getTitleColumnhidden(),
                'container_link_title' => $this->getTitleColumn(),

                'container_file' => $this->getVideoColumn(),
               'container_thumb_file' => $this->getThumbColumn(),
                'featured' => $this->getFeaturedColumn(),
               'status' => $this->getStatusColumn1(),
                'position' => $recordPosition,
                'action_delete' => $recordActionDelete,
                


              
            ]
        );
    }
    /*
    */
    /*
    <field name="video_id">
            <argument name="data" xsi:type="array">
                <item name="config" xsi:type="array">
                    <item name="visible" xsi:type="boolean">false</item>
                    <item name="dataType" xsi:type="string">text</item>
                    <item name="formElement" xsi:type="string">input</item>
                    <item name="source" xsi:type="string">productvideos</item>
                    <item name="dataScope" xsi:type="string">video_id</item>
                </item>
            </argument>
        </field>
    */
    /**
     * @return array
     */
    protected function getTitleColumnhidden2()
    {
        $titleContainer['arguments']['data']['config'] = [
            'componentType' => Container::NAME,
            'formElement' => Container::NAME,
            'component' => 'Magento_Ui/js/form/components/group',
            'label' => __('Title'),
            'dataScope' => '',
            'visible' => false,
        ];
        $titleField['arguments']['data']['config'] = [
            'formElement' => Form\Element\Input::NAME,
            'componentType' => Form\Field::NAME,
            'dataType' => Form\Element\DataType\Text::NAME,
            'visible' => false,
            'dataScope' => 'video_id',
            
        ];

        return $this->arrayManager->set('children/link_title', $titleContainer, $titleField);
    }
    protected function getTitleColumnhidden()
    {
        $titleContainer['arguments']['data']['config'] = [
            'componentType' => Container::NAME,
            'formElement' => Container::NAME,
            'component' => 'Magento_Ui/js/form/components/group',
            'label' => __('Title'),
            'dataScope' => '',
            'visible' => false,
        ];
        $titleField['arguments']['data']['config'] = [
            'formElement' => Form\Element\Input::NAME,
            'componentType' => Form\Field::NAME,
            'dataType' => Form\Element\DataType\Text::NAME,
            'visible' => false,
            'dataScope' => 'youtube_thumb',
            
        ];

        return $this->arrayManager->set('children/link_title', $titleContainer, $titleField);
    }
    protected function getTitleColumn()
    {
        $titleContainer['arguments']['data']['config'] = [
            'componentType' => Container::NAME,
            'formElement' => Container::NAME,
            'component' => 'Magento_Ui/js/form/components/group',
            'label' => __('Title'),
            'dataScope' => '',
        ];
        $titleField['arguments']['data']['config'] = [
            'formElement' => Form\Element\Input::NAME,
            'componentType' => Form\Field::NAME,
            'dataType' => Form\Element\DataType\Text::NAME,
            'dataScope' => 'title',
            'validation' => [
                'required-entry' => true,
            ],
        ];

        return $this->arrayManager->set('children/link_title', $titleContainer, $titleField);
    }

    protected function getCustomerGroup()
    {
        $titleContainer['arguments']['data']['config'] = [
            'componentType' => Container::NAME,
            'formElement' => Container::NAME,
            'component' => 'Magento_Ui/js/form/components/group',
            'label' => __('Customer Group'),
            'dataScope' => '',
        ];
        $titleField['arguments']['data']['config'] = [
            'formElement' => Form\Element\MultiSelect::NAME,
            'componentType' => Form\Field::NAME,
            'dataType' => Form\Element\DataType\Text::NAME,
            'dataScope' => 'customer_group',
            'options' => $this->cgroups->toOptionArray(),
            
        ];

        return $this->arrayManager->set('children/customer_group', $titleContainer, $titleField);
    }


    protected function getStatusColumn()
    {
        $titleContainer['arguments']['data']['config'] = [
        'componentType' => Container::NAME,
        'formElement' => Container::NAME,
        'component' => 'Magento_Ui/js/form/components/group',
        'label' => __('Enable'),
        'dataScope' => '',
        ];
        $titleField['arguments']['data']['config'] = [
        'formElement' => Form\Element\Select::NAME,
        'componentType' => Form\Field::NAME,
        'dataType' => Form\Element\DataType\Text::NAME,
        'dataScope' => 'status',
        'options' => $this->_getOptions(),
        ];

        return $this->arrayManager->set('children/link_title', $titleContainer, $titleField);
    }
    /**
     * @return array
     */
    

    /**
     * @return array
     */
    
    protected function getFeaturedColumn()
    {
        $shareableField['arguments']['data']['config'] = [
            'label' => __('Featured ?'),
            'formElement' => Form\Element\Select::NAME,
            'componentType' => Form\Field::NAME,
            'dataType' => Form\Element\DataType\Number::NAME,
            'dataScope' => 'featured',
            'default' => '0',
            'options' => $this->typeUpload->toOption2Array(),
        ];

        return $shareableField;
    }
    protected function getStatusColumn1()
    {
        $shareableField['arguments']['data']['config'] = [
            'label' => __('Status'),
            'formElement' => Form\Element\Select::NAME,
            'componentType' => Form\Field::NAME,
            'dataType' => Form\Element\DataType\Number::NAME,
            'dataScope' => 'status',
            'options' => $this->typeUpload->toOption2Array(),
        ];

        return $shareableField;
    }
    /*
    <field name="youtube_thumb">
            <argument name="data" xsi:type="array">
                <item name="config" xsi:type="array">
                    <item name="dataType" xsi:type="string">text</item>
                    <item name="label" xsi:type="string" translate="true">youtube_thumb</item>
                    <item name="formElement" xsi:type="string">input</item>
                    <item name="source" xsi:type="string">mediaappearance</item>
                     <item name="visible" xsi:type="boolean">false</item>
                    <item name="dataScope" xsi:type="string">youtube_thumb</item>
                    
                </item>
            </argument>
        </field>
    */
    
    protected function getThumbColumn()
    {
        $fileContainer['arguments']['data']['config'] = [
            'componentType' => Container::NAME,
            'formElement' => Container::NAME,
            'component' => 'Magento_Ui/js/form/components/group',
            'label' => __('Upload Thumb'),
            'dataScope' => '',
        ];
        $fileTypeField['arguments']['data']['config'] = [
            'formElement' => Form\Element\Select::NAME,
            'componentType' => Form\Field::NAME,
            'component' => 'FME_Mediaappearance/projs/components/upload-type-handler',
            'dataType' => Form\Element\DataType\Text::NAME,
            'dataScope' => 'type',
            'options' => $this->typeUpload->toOptionArray(),
            'typeFile' => 'links_file',
            'typeUrl' => 'link_url',
        ];
        $fileLinkUrl['arguments']['data']['config'] = [
            'formElement' => Form\Element\Input::NAME,
            'componentType' => Form\Field::NAME,
            'dataType' => Form\Element\DataType\Text::NAME,
            'dataScope' => 'link_url',
            'placeholder' => 'URL',
           // 'notice' => '(In URL field put youtube or Vimeo URL OR complete path of video e.g http://www.domain.com/media/abc.flv)',
            'validation' => [
                'required-entry' => true,
                'validate-url' => true,
            ],
        ];
        $fileUploader['arguments']['data']['config'] = [
            'formElement' => 'fileUploader',
            'componentType' => 'fileUploader',
            'component' => 'FME_Mediaappearance/projs/components/file-uploader',
            'elementTmpl' => 'FME_Mediaappearance/components/file-uploader',
            'fileInputName' => 'filethumb',
           // 'notice' => 'Supported Format FLV, MPEG, MP4, MP3 (Your Servers php maximum upload size must be greater than file size',
          // 'renderer'  => 'FME\Mediaappearance\Block\Adminhtml\Quotation\Renderer\LogoImage',
           'uploaderConfig' => [
                'url' => 'mediaappearanceadmin/mediaappearance/uploadvideo',
            ],
            'dataScope' => 'filethumb',
            'validation' => [
                'required-entry' => true,
            ],
        ];

        return $this->arrayManager->set(
            'children',
            $fileContainer,
            [
                
                'links_file' => $fileUploader
            ]
        );
    }
   
    protected function getVideoColumn()
    {
        $fileContainer['arguments']['data']['config'] = [
            'componentType' => Container::NAME,
            'formElement' => Container::NAME,
            'component' => 'Magento_Ui/js/form/components/group',
            'label' => __('Upload Video'),
            'dataScope' => '',
        ];
        $fileTypeField['arguments']['data']['config'] = [
            'formElement' => Form\Element\Select::NAME,
            'componentType' => Form\Field::NAME,
            'component' => 'FME_Mediaappearance/projs/components/upload-type-handler',
            'dataType' => Form\Element\DataType\Text::NAME,
            'dataScope' => 'type',
            'options' => $this->typeUpload->toOptionArray(),
            'typeFile' => 'links_file',
            'typeUrl' => 'link_url',
        ];
        $fileLinkUrl['arguments']['data']['config'] = [
            'formElement' => Form\Element\Input::NAME,
            'componentType' => Form\Field::NAME,
            'dataType' => Form\Element\DataType\Text::NAME,
            'dataScope' => 'link_url',
            'placeholder' => 'URL',
            'component' => 'FME_Mediaappearance/js/form/element/urlvisible',
           // 'notice' => '(In URL field put youtube or Vimeo URL OR complete path of video e.g http://www.domain.com/media/abc.flv)',
            'validation' => [
                'required-entry' => true,
                'validate-url' => true,
            ],
        ];
        $fileUploader['arguments']['data']['config'] = [
            'formElement' => 'fileUploader',
            'componentType' => 'fileUploader',
            'component' => 'FME_Mediaappearance/projs/components/file-uploader',
            'elementTmpl' => 'FME_Mediaappearance/components/file-uploader',
            'fileInputName' => 'filename',
           // 'notice' => 'Supported Format FLV, MPEG, MP4, MP3 (Your Servers php maximum upload size must be greater than file size',
            'uploaderConfig' => [
                'url' =>'mediaappearanceadmin/mediaappearance/uploadvideo',
            ],
            'dataScope' => 'filename',
            'validation' => [
                'required-entry' => true,
            ],
        ];

        return $this->arrayManager->set(
            'children',
            $fileContainer,
            [
                'type' => $fileTypeField,
                'link_url' => $fileLinkUrl,
                'links_file' => $fileUploader
            ]
        );
    }

    protected function _getOptions()
    {
        $options = [
            1 => [
                'label' => __('Yes'),
                'value' => 1
            ],
            2 => [
                'label' => __('No'),
                'value' => 0
            ],
        ];
 
        return $options;
    }
}