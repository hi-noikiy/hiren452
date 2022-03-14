<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-dynamic-category
 * @version   1.0.17
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */



declare(strict_types=1);

namespace Mirasvit\DynamicCategory\Block\Adminhtml\Category\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Extended as ProductTab;
use Magento\Backend\Helper\Data;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Mirasvit\DynamicCategory\Registry;

class Product extends ProductTab
{
    private $collectionFactory;

    private $productFactory;

    private $registry;

    private $status;

    private $visibility;

    public function __construct(
        Registry $registry,
        CollectionFactory $collectionFactory,
        Context $context,
        Data $backendHelper,
        ProductFactory $productFactory,
        array $data = [],
        Visibility $visibility = null,
        Status $status = null
    ) {
        parent::__construct($context, $backendHelper, $data);

        $this->collectionFactory = $collectionFactory;
        $this->productFactory    = $productFactory;
        $this->registry          = $registry;
        $this->status            = $status;
        $this->visibility        = $visibility;
    }

    public function getGridUrl(): string
    {
        return $this->getUrl('catalog/*/grid', ['_current' => true]);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('catalog_category_products');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
    }

    protected function _prepareCollection(): Product
    {
        if ($this->registry->getCurrentDynamicCategory()) {
            /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
            $collection = $this->productFactory->create()->getCollection()
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('sku')
                ->addAttributeToSelect('visibility')
                ->addAttributeToSelect('status')
                ->addAttributeToSelect('price');

            $collection->getSelect()->columns(
                [
                    'position' => new \Zend_Db_Expr(0),
                ]
            );

            $storeId = (int)$this->getRequest()->getParam('store', 0);
            if ($storeId > 0) {
                $collection->addStoreFilter($storeId);
            }

            $category = $this->registry->getCurrentDynamicCategory();
            $category->getRule()->applyToFullCollection($collection);

            $this->setCollection($collection);
        }

        parent::_prepareCollection();

        return $this;
    }

    protected function _prepareColumns(): Product
    {
        $this->addColumn(
            'entity_id',
            [
                'header'           => __('ID'),
                'sortable'         => true,
                'index'            => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
            ]
        );

        $this->addColumn('name', ['header' => __('Name'), 'index' => 'name']);
        $this->addColumn('sku', ['header' => __('SKU'), 'index' => 'sku']);

        $this->addColumn(
            'visibility',
            [
                'header'           => __('Visibility'),
                'index'            => 'visibility',
                'type'             => 'options',
                'options'          => Visibility::getOptionArray(),
                'header_css_class' => 'col-visibility',
                'column_css_class' => 'col-visibility',
            ]
        );

        $this->addColumn(
            'status',
            [
                'header'  => __('Status'),
                'index'   => 'status',
                'type'    => 'options',
                'options' => Status::getOptionArray(),
            ]
        );

        $this->addColumn(
            'price',
            [
                'header'        => __('Price'),
                'type'          => 'currency',
                'currency_code' => (string)$this->_scopeConfig->getValue(
                    \Magento\Directory\Model\Currency::XML_PATH_CURRENCY_BASE,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                ),
                'index'         => 'price',
            ]
        );

        $this->addColumn(
            'position',
            [
                'header'   => __('Position'),
                'type'     => 'number',
                'index'    => 'position',
                'editable' => true,
            ]
        );

        return parent::_prepareColumns();
    }
}
