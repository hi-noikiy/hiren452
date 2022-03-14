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
namespace Magedelight\Facebook\Block\Adminhtml;

use Magento\Backend\Block\Template\Context;
use Magedelight\Facebook\Model\AttributemapFactory;
use Magedelight\Facebook\Model\FbattributesFactory;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory;

class Importproduct extends \Magento\Backend\Block\Template {
    
    /**
     *
     * @var AttributemapFactory 
     */
    protected $attributemapFactory;
    
    /**
     *
     * @var FbattributesFactory 
     */
    protected $fbattributesFactory;
    
    /**
     * 
     * @param Context $context
     * @param AttributemapFactory $attributemapFactory
     * @param FbattributesFactory $fbattributesFactory
     */
    public function __construct(
        Context $context,
        AttributemapFactory $attributemapFactory,
        FbattributesFactory $fbattributesFactory,
        CollectionFactory $collectionFactory    
    ) {
        $this->attributemapFactory = $attributemapFactory;
        $this->fbattributesFactory = $fbattributesFactory;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }
    
    protected function _prepareLayout()
    {
        $this->setChild('product_import_button',
            $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
                ->setData(array(
                    'label' => __('Import'),
                    'class' => 'import icon-btn primary',
                    'on_click' => 'importCsv()',
                ))
        );
        parent::_prepareLayout();
    }
    
    public function getFeedProductImportButtonHtml()
    {
        return $this->getChildHtml('product_import_button');
    }
}
