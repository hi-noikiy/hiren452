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
namespace Magedelight\Facebook\Ui\Component\MassAction\Facebook;
 
use Magento\Framework\UrlInterface;
use Zend\Stdlib\JsonSerializable;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;
 
/**
* Class Options
*/
class Options implements JsonSerializable
{
   /**
    * @var array
    */
   protected $options;
 
   /**
    * Additional options params
    *
    * @var array
    */
   protected $data;
 
   /**
    * @var UrlInterface
    */
   protected $urlBuilder;
 
   /**
    * Base URL for subactions
    *
    * @var string
    */
   protected $urlPath;
 
   /**
    * Param name for subactions
    *
    * @var string
    */
   protected $paramName;
 
   /**
    * Additional params for subactions
    *
    * @var array
    */
   protected $additionalData = [];
 
   /**
    * Constructor
    *
    * @param CollectionFactory $collectionFactory
    * @param UrlInterface $urlBuilder
    * @param array $data
    */
   public function __construct(
       UrlInterface $urlBuilder,
       array $data = []
   ) {
       $this->data = $data;
       $this->urlBuilder = $urlBuilder;
   }
 
   /**
    * Get action options
    *
    * @return array
    */
   public function jsonSerialize()
   {
       if ($this->options === null) {
           $options = array(
               array(
                   "value" => Boolean::VALUE_YES,
                   "label" => __('Yes'),
               ),
               array(
                   "value" => Boolean::VALUE_NO,
                   "label" => __('No'),
               )
           );
           $this->prepareData();
           foreach ($options as $optionCode) {
               $this->options[$optionCode['value']] = [
                   'type' => 'status_' . $optionCode['value'],
                   'label' => $optionCode['label'],
               ];
 
               if ($this->urlPath && $this->paramName) {
                   $this->options[$optionCode['value']]['url'] = $this->urlBuilder->getUrl(
                       $this->urlPath,
                       [$this->paramName => $optionCode['value']]
                   );
               }
 
               $this->options[$optionCode['value']] = array_merge_recursive(
                   $this->options[$optionCode['value']],
                   $this->additionalData
               );
           }
           $this->options = array_values($this->options);
       }
       return $this->options;
   }
 
   /**
    * Prepare addition data for subactions
    *
    * @return void
    */
   protected function prepareData()
   {
       foreach ($this->data as $key => $value) {
           switch ($key) {
               case 'urlPath':
                   $this->urlPath = $value;
                   break;
               case 'paramName':
                   $this->paramName = $value;
                   break;
               default:
                   $this->additionalData[$key] = $value;
                   break;
           }
       }
   }
}