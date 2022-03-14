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

namespace Magezon\ProductPagePdf\Block\Product\Element;

class StockStatus extends \Magezon\ProductPagePdf\Block\Product\Element
{
    /**
	 * @var \Magento\CatalogInventory\Model\Stock\StockItemRepository
	 */
    protected $stockItemRepository;
    
    /**
	 * @var \Magento\CatalogInventory\Api\Data\StockItemInterface
	 */
	protected $stockItem;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository,
        array $data = []
    ) {
        parent::__construct($context, $registry, $data);
        $this->stockItemRepository = $stockItemRepository;
    }

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        if ($this->getStockItem()) {
            return parent::isEnabled();
        }
        return false;
    }
    
    /**
     * @return \Magento\CatalogInventory\Api\Data\StockItemInterface
     */
    public function getStockItem()
    {
        if ($this->stockItem == null) {
            try {
                $productId = $this->getProductId();
                $this->stockItem = $this->stockItemRepository->get($productId);
            } catch (\Exception $e) {

            }
        }
        return $this->stockItem;
    }
}
