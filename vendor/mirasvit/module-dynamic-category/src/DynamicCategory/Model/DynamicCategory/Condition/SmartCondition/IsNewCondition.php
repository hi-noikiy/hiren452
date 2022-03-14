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

namespace Mirasvit\DynamicCategory\Model\DynamicCategory\Condition\SmartCondition;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\Model\AbstractModel;
use Magento\Rule\Model\Condition\Context;
use Mirasvit\DynamicCategory\Model\DynamicCategory\Condition\QueryBuilder;

class IsNewCondition extends AbstractCondition
{
    const ATTR_FROM   = 'news_from_date';
    const ATTR_TO     = 'news_to_date';
    const ATTR_IS_NEW = 'is_new';
    const ATTR_NEW    = 'new';

    private $queryBuilder;

    public function __construct(
        QueryBuilder $queryBuilder,
        Context $context,
        array $data = []
    ) {
        $this->queryBuilder = $queryBuilder;

        parent::__construct($context, $data);
    }

    public function getAttributeElementHtml(): string
    {
        return (string)__('Is New');
    }

    public function getInputType(): string
    {
        return 'select';
    }

    public function getValueElementType(): string
    {
        return 'select';
    }

    public function getValueSelectOptions(): array
    {
        return [
            ['value' => 1, 'label' => 'Yes'],
            ['value' => 0, 'label' => 'No'],
        ];
    }

    public function collectValidatedAttributes(Collection $collection): IsNewCondition
    {
        $this->queryBuilder->joinField($collection->getSelect(), self::ATTR_FROM);
        $this->queryBuilder->joinField($collection->getSelect(), self::ATTR_TO);
        $this->queryBuilder->joinField($collection->getSelect(), self::ATTR_NEW);
        $this->queryBuilder->joinField($collection->getSelect(), self::ATTR_IS_NEW);

        return $this;
    }

    public function validate(AbstractModel $model): bool
    {
        $from = $model->getData(self::ATTR_FROM) ? strtotime($model->getData(self::ATTR_FROM)) : null;
        $to   = $model->getData(self::ATTR_TO) ? strtotime($model->getData(self::ATTR_TO)) : null;

        $isNew = $this->isNew((int)$from, (int)$to);

        if ($model->getData(self::ATTR_IS_NEW)) {
            $isNew = true;
        }

        if ($model->getData(self::ATTR_NEW)) {
            $isNew = true;
        }

        return $this->getOperator() === '==' ? $isNew : !$isNew;
    }

    private function isNew(int $from, int $to): bool
    {
        $isNew = true;

        if (!$from && !$to) {
            $isNew = false;
        } elseif ($from && $from > time()) {
            $isNew = false;
        } elseif ($to && time() > $to) {
            $isNew = false;
        }

        return $isNew;
    }
}
