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

namespace Mirasvit\DynamicCategory\Api\Data;

use Mirasvit\DynamicCategory\Model\DynamicCategory\Rule;

interface DynamicCategoryInterface
{
    const TABLE_NAME = 'mst_dynamic_category';

    const RULE_FORM_NAME = 'category_form'; // should be the same as in Magento

    const ID                    = 'dynamic_category_id';
    const CATEGORY_ID           = 'category_id';
    const CONDITIONS_SERIALIZED = 'conditions_serialized';
    const IS_ACTIVE             = 'is_active';

    /**
     * @return int
     */
    public function getId(): int;

    /**
     * @return int
     */
    public function getCategoryId(): int;

    /**
     * @param int $id
     *
     * @return DynamicCategoryInterface
     */
    public function setCategoryId(int $id): DynamicCategoryInterface;

    /**
     * @return string
     */
    public function getConditionsSerialized(): string;

    /**
     * @param string $conditions
     *
     * @return DynamicCategoryInterface
     */
    public function setConditionsSerialized(string $conditions): DynamicCategoryInterface;

    /**
     * @return bool
     */
    public function getIsActive(): bool;

    /**
     * @param bool $flag
     *
     * @return DynamicCategoryInterface
     */
    public function setIsActive(bool $flag): DynamicCategoryInterface;

    /**
     * @return Rule
     */
    public function getRule(): Rule;
}
