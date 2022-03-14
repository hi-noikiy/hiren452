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

namespace Mirasvit\DynamicCategory\Plugin\Backend;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\DB\Select;
use Mirasvit\DynamicCategory\Registry;

/**
 * @see Collection::getSelectCountSql()
 */
class FixProductCountSqlPlugin
{
    private $registry;

    public function __construct(
        Registry $registry
    ) {
        $this->registry = $registry;
    }

    public function afterGetSelectCountSql(Collection $subject, Select $select): Select
    {
        $this->apply($select);

        return $select;
    }

    private function apply(Select $select): void
    {
        if ($this->registry->getIsGetSizeResetGroup()) {
            $select->reset(\Magento\Framework\DB\Select::GROUP);
        }
    }
}
