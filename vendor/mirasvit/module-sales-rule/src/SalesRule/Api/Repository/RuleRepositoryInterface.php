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



namespace Mirasvit\SalesRule\Api\Repository;

use Mirasvit\SalesRule\Api\Data\RuleInterface;

interface RuleRepositoryInterface
{
    /**
     * @return RuleInterface[]|\Mirasvit\SalesRule\Model\ResourceModel\Rule\Collection
     */
    public function getCollection();

    /**
     * @return RuleInterface
     */
    public function create();

    /**
     * @param int $id
     *
     * @return RuleInterface|false
     */
    public function get($id);

    /**
     * @param int $id
     *
     * @return RuleInterface|false
     */
    public function getByParentId($id);

    /**
     * @param RuleInterface $rule
     *
     * @return RuleInterface
     */
    public function save(RuleInterface $rule);

    /**
     * @param RuleInterface $rule
     *
     * @return $this
     */
    public function delete(RuleInterface $rule);
}