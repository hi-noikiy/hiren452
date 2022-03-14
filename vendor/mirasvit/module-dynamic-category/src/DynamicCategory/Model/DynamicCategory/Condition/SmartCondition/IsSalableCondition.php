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

use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\Model\AbstractModel;
use Magento\Rule\Model\Condition\Context;

class IsSalableCondition extends AbstractCondition
{
    private $productRepository;

    public function __construct(
        ProductRepository $productRepository,
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->productRepository = $productRepository;
    }

    public function getAttributeElementHtml(): string
    {
        return (string)__('Is Salable');
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

    public function validate(AbstractModel $model): bool
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->productRepository->get($model->getSku());

        // we use string value due to strict compare
        return $this->validateAttribute($product->isSalable() ? '1' : '0');
    }

    public function collectValidatedAttributes(Collection $collection): IsSalableCondition
    {
        return $this;
    }
}
