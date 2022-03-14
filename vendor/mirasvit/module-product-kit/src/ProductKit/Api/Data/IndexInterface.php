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



namespace Mirasvit\ProductKit\Api\Data;

interface IndexInterface
{
    const TABLE_NAME = 'mst_product_kit_index';

    const ID          = 'index_id';
    const KIT_ID      = 'kit_id';
    const STORE_ID    = 'store_id';
    const ITEM_ID     = 'item_id';
    const PRODUCT_ID  = 'product_id';
    const POSITION    = 'position';
    const IS_OPTIONAL = 'is_optional';
    const IS_PRIMARY  = 'is_primary';
}
