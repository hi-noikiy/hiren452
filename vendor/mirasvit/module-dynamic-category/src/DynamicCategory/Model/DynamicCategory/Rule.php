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

namespace Mirasvit\DynamicCategory\Model\DynamicCategory;

use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\Iterator;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Rule\Model\AbstractModel;
use Mirasvit\DynamicCategory\Service\ReindexService;

class Rule extends AbstractModel
{
    const FORM_NAME = 'category_form';

    private $productIds = [];

    private $combineFactory;

    private $iterator;

    private $metadataPool;

    private $productFactory;

    private $reindexService;

    public function __construct(
        Condition\CombineFactory $combineFactory,
        Iterator $iterator,
        MetadataPool $metadataPool,
        ProductFactory $productFactory,
        Context $context,
        Registry $registry,
        ReindexService $reindexService,
        FormFactory $formFactory,
        TimezoneInterface $localeDate
    ) {
        $this->combineFactory = $combineFactory;
        $this->iterator       = $iterator;
        $this->metadataPool   = $metadataPool;
        $this->productFactory = $productFactory;
        $this->reindexService = $reindexService;

        parent::__construct($context, $registry, $formFactory, $localeDate);
    }

    public function getActionsInstance(): void
    {
    }

    public function getConditionsInstance(): Condition\Combine
    {
        return $this->combineFactory->create();
    }

    public function applyToCollection(Collection $collection): void
    {
        $ids   = $this->getMatchingProductIds($collection, true);
        $ids[] = 0;

        $collection->addFieldToFilter('entity_id', ['in' => $ids]);
    }

    public function applyToFullCollection(Collection $collection): void
    {
        $ids   = $this->getMatchingProductIds($collection, false);
        $ids[] = 0;

        $collection->addFieldToFilter('entity_id', ['in' => $ids]);
    }

    public function getMatchingProductIds(Collection $collection, bool $limitCollection = true): array
    {
        $this->productIds = [];

        $this->getConditions()->applyConditions($collection);
        $this->getConditions()->collectValidatedAttributes($collection);

        $collection->getSelect()->group('e.entity_id');

        if ($limitCollection && $collection->count() > 1000) {
            throw new LocalizedException(__("Number of products is too match for processing. Please run reindex."));
        }

        $this->iterator->walk(
            $collection->getSelect(),
            [[$this, 'callbackValidateProduct']],
            [
                'product' => $this->productFactory->create(),
            ]
        );

        $this->productIds = array_unique($this->productIds);

        return $this->productIds;
    }

    public function callbackValidateProduct(array $args): void
    {
        $product = clone $args['product'];
        $product->setData($args['row']);

        if ($this->getConditions()->validate($product)) {
            $this->productIds[] = (int)$product->getId();
        }
    }
}
