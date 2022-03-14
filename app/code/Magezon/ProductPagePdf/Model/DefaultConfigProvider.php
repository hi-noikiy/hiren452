<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://www.magezon.com/license
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_ProductPagePdf
 * @copyright Copyright (C) 2020 Magezon (https://www.magezon.com)
 */

namespace Magezon\ProductPagePdf\Model;

use Magento\Framework\App\ObjectManager;

class DefaultConfigProvider extends \Magezon\Builder\Model\DefaultConfigProvider
{
    /**
     * @var string
     */
    protected $_builderArea = 'product_pdf';

   /**
    * @var \Magezon\Builder\Data\Elements
    */
   protected $builderElements = 'product_pdf';

   /**
    * @param \Magezon\Builder\Model\WysiwygConfigProvider $wysiwygConfig
    * @param \Magezon\Builder\Model\CacheManager          $builderCacheManager
    * @param \Magezon\Builder\Data\Groups                 $builderGroups
    * @param \Magezon\Builder\Data\Elements               $builderElements
    * @param \Magezon\Builder\Helper\Data                 $builderHelper
    * @param \Magezon\SimpleBuilder\Data\Elements         $builderPdfHelper
    */
   public function __construct(
       \Magezon\Builder\Model\WysiwygConfigProvider $wysiwygConfig,
       \Magezon\Builder\Model\CacheManager $builderCacheManager,
       \Magezon\Builder\Data\Groups $builderGroups,
       \Magezon\Builder\Data\Elements $builderElements,
       \Magezon\Builder\Helper\Data $builderHelper,
       \Magezon\SimpleBuilder\Data\Elements $builderPdfHelper
   ) {
       parent::__construct($wysiwygConfig, $builderCacheManager, $builderGroups, $builderElements, $builderHelper);
       $this->builderElements = $builderPdfHelper;
   }

    /**
     * @return array
     */
    public function getConfig()
    {
        $config = parent::getConfig();
        $config['profile'] = [
            'builder'     => 'Magezon\ProductPagePdf\Block\Builder',
            'home'        => '#',
            'templateUrl' => '#'
        ];
       $config['loadStylesUrl'] = 'mgzsimplebuilder/ajax/loadStyles';
        return $config;
    }

   /**
    * @return string
    */
   public function getCacheKey()
   {
       return 'MAGEZON_SIMPLE_BUILDER_CONFIG' . $this->getBuilderArea();
   }
}
