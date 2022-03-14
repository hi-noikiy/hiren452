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
 * @package   mirasvit/module-sales-rule
 * @version   1.0.16
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SalesRule\Repository;

use Mirasvit\SalesRule\Api\Repository\RuleTypeRepositoryInterface;

class RuleTypeRepository implements RuleTypeRepositoryInterface
{
    /**
     * @var array
     */
    private $pool;

    /**
     * RuleTypeRepository constructor.
     * @param array $pool
     */
    public function __construct(
        $pool = []
    ) {
        $this->pool = $pool;
    }

    /**
     * @return array|\Mirasvit\SalesRule\Api\Data\RuleTypeInterface[]
     */
    public function getList()
    {
        return $this->pool;
    }
}