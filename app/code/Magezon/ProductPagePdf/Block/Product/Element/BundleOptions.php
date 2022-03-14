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

class BundleOptions extends \Magezon\ProductPagePdf\Block\Product\Element
{
    /**
     * @var \Magento\Bundle\Model\ResourceModel\Selection\Collection
     */
    protected $collection;

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        if ($this->getProduct()->getTypeId() == \Magento\Bundle\Model\Product\Type::TYPE_CODE) {
            if ($this->getSelectionsCollection()->count()) {
                return parent::isEnabled();
            }
        }
        return false;
    }

    /**
     * @return \Magento\Bundle\Model\ResourceModel\Selection\Collection
     */
    public function getSelectionsCollection()
    {
        if ($this->collection == null) {
            $product = $this->getProduct();
            $collection = $product->getTypeInstance(true)
                    ->getSelectionsCollection(
                        $product->getTypeInstance(true)->getOptionsIds($product), 
                        $product
                    );
            $this->collection = $collection;
        }
       return $this->collection;
    }
}
