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
 * @package   mirasvit/module-product-kit
 * @version   1.0.29
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\ProductKit\Ui\Kit\Form;

use Magento\Catalog\Model\ResourceModel\Product\Collection;

class ProductDataProvider extends \Magento\Catalog\Ui\DataProvider\Product\ProductDataProvider
{
    public function getCollection()
    {
        /** @var Collection $collection */
        $collection = parent::getCollection();

        return $collection->addAttributeToSelect('status');
    }

    public function getMeta()
    {
        $meta = parent::getMeta();

        if (isset($meta['listing_top'])) {
            unset($meta['listing_top']);
        }

        return $meta;
    }
}
