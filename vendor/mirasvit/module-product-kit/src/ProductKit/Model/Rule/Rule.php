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



namespace Mirasvit\ProductKit\Model\Rule;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Rule\Model\AbstractModel;

class Rule extends AbstractModel
{
    const FORM_NAME = 'product_kit_kit_form';

    private $combineFactory;

    private $elementEname = '';

    public function __construct(
        Condition\CombineFactory $combineFactory,
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        TimezoneInterface $localeDate
    ) {
        $this->combineFactory = $combineFactory;

        parent::__construct($context, $registry, $formFactory, $localeDate);
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setElementName($name)
    {
        $this->elementEname = $name;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getActionsInstance()
    {
    }

    /**
     * {@inheritDoc}
     */
    public function getConditionsInstance()
    {
        return $this->combineFactory->create(['elementName' => $this->elementEname]);
    }

    public function hasConditionsSerialized()
    {
        return true;
    }

    /**
     * @param Collection $collection
     * @return array
     */
    public function getMatchedProductIds(Collection $collection)
    {
        $this->getConditions()->applyConditions($collection);

        $ids = [];
        foreach ($collection as $item) {
            $ids[] = $item->getId();
        }

        return $ids;
    }

    /**
     * @param array $data
     * @return string
     */
    public function buildPostSmartConditions($data)
    {
        $conditions = [];
        foreach ($data as $position => $condition) {
            $builtData = $this->_convertFlatToRecursive($condition);
            if (isset($builtData['conditions'][1])) {
                $conditions[$position] = $builtData['conditions'][1];
            }
        }

        return $conditions;
    }
}
