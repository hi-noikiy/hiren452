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

namespace Magezon\ProductPagePdf\Ui\Component\Form\Element;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Ui\Component\Wysiwyg\ConfigInterface;

class Builder extends \Magezon\Builder\Ui\Component\Form\Element\Builder
{
    /**
     * @param ContextInterface $context
     * @param FormFactory $formFactory
     * @param ConfigInterface $wysiwygConfig
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param \Magento\Framework\Registry $registry
     * @param array $components
     * @param array $data
     * @param array $config
     */
	public function __construct(
        ContextInterface $context,
        FormFactory $formFactory,
        ConfigInterface $wysiwygConfig,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Framework\Registry $registry,
        array $components = [],
        array $data = [],
        array $config = []
    ) {
        $data['config']['previewUrl'] = $context->getUrl('productpagepdf/pdf/preview');
        $data['config']['searchUrl']  = $context->getUrl('productpagepdf/pdf/search');
        $collection = $productCollection->create();
        $collection->addAttributeToSelect('name');
        $product = $collection->getFirstItem();
        if ($product->getId()) {
            $data['config']['entityId'] = $product->getId();
            $data['config']['search']   = $product->getName();
        }
    	parent::__construct($context, $formFactory, $wysiwygConfig, $layoutFactory, $registry, $components, $data, $config);
	}
}
