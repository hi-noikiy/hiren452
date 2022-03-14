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

namespace Mirasvit\DynamicCategory\Model\DynamicCategory\Condition\Msi;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\Model\AbstractModel;
use Magento\Rule\Model\Condition\Context;
use Mirasvit\DynamicCategory\Model\DynamicCategory\Condition\QueryBuilder;

class SourceCondition extends AbstractCondition
{
    const ATTR_PRICE       = 'price';
    const ATTR_FINAL_PRICE = 'final_price';

    /**
     * @var mixed
     */
    private $sourceCollectionFactory;

    private $queryBuilder;

    public function __construct(
        QueryBuilder $queryBuilder,
        Context $context,
        array $data = []
    ) {
        $this->queryBuilder = $queryBuilder;

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        try {
            $this->sourceCollectionFactory = $objectManager->create('Magento\Inventory\Model\ResourceModel\Source\CollectionFactory');
        } catch (\Exception $e) {}

        parent::__construct($context, $data);
    }

    public function getAttributeElementHtml(): string
    {
        return (string)__('Source');
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
        $options = [];

        if ($this->sourceCollectionFactory) {
            foreach ($this->sourceCollectionFactory->create() as $source) {
                $options[] = [
                    'label' => $source->getName(),
                    'value' => $source->getSourceCode(),
                ];
            }
        }

        return $options;
    }

    public function collectValidatedAttributes(Collection $collection): self
    {
        $tableAlias = 'inventory_source_item' . hash('sha256', (string)microtime(true));

        $collection->getSelect()->joinLeft([
            $tableAlias => $this->queryBuilder->getResource()->getTableName('inventory_source_item'),
        ], 'e.sku = ' . $tableAlias . '.sku AND status = 1 AND ' . $tableAlias . '.source_code = "' . $this->getValue() . '"', [
            'source_code',
        ])->where($tableAlias . '.source_code = "' . $this->getValue() . '"');

        return $this;
    }

    public function validate(AbstractModel $model): bool
    {
        return $this->validateAttribute($model->getData('source_code'));
    }
}
