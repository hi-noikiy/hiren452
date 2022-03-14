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



namespace Mirasvit\ProductKit\Model\ResourceModel\Kit;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Mirasvit\ProductKit\Api\Data\KitInterface;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            \Mirasvit\ProductKit\Model\Kit::class,
            \Mirasvit\ProductKit\Model\ResourceModel\Kit::class
        );
    }

    /**
     * @param int $id
     * @return $this
     */
    public function addFilterByStoreId($id)
    {
        $this->getSelect()->where('(find_in_set(?, ' . KitInterface::STORE_IDS . ') OR ' . KitInterface::STORE_IDS . ' = 0)', $id);

        return $this;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function addFilterByCustomerGroupId($id)
    {
        $this->getSelect()->where('find_in_set(?, ' . KitInterface::CUSTOMER_GROUP_IDS . ')', $id);

        return $this;
    }
}
