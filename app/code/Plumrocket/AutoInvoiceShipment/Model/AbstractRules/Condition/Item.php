<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package   Plumrocket_AutoInvoiceShipment
 * @copyright Copyright (c) 2017 Plumrocket Inc. (http://www.plumrocket.com)
 * @license   http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\AutoInvoiceShipment\Model\AbstractRules\Condition;

use Magento\SalesRule\Model\Rule\Condition\Product;

use Magento\Sales\Model\Order\Item as OrderItem;
use \Magento\Rule\Model\Condition\Product\AbstractProduct;

/**
 * Class Item
 *
 * @method array getAttributeOption()
 * @method array getAdditionalOptions()
 *
 * @method $this setType($type)
 * @method $this setAttributeOption($attributes)
 * @method $this setAdditionalOptions(array $options)
 */
class Item extends Product
{
    /**
     * Item constructor.
     *
     * @param \Magento\Rule\Model\Condition\Context                            $context
     * @param \Magento\Backend\Helper\Data                                     $backendData
     * @param \Magento\Eav\Model\Config                                        $config
     * @param \Magento\Catalog\Model\ProductFactory                            $productFactory
     * @param \Magento\Catalog\Api\ProductRepositoryInterface                  $productRepository
     * @param \Magento\Catalog\Model\ResourceModel\Product                     $productResource
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection $attrSetCollection
     * @param \Magento\Framework\Locale\FormatInterface                        $localeFormat
     * @param array                                                            $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Backend\Helper\Data $backendData,
        \Magento\Eav\Model\Config $config,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Model\ResourceModel\Product $productResource,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection $attrSetCollection,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $backendData,
            $config,
            $productFactory,
            $productRepository,
            $productResource,
            $attrSetCollection,
            $localeFormat,
            $data
        );
        $this->setType('Plumrocket\AutoInvoiceShipment\Model\AbstractRules\Condition\Item');
    }

    /**
     * Validate item in order
     *
     * @param  \Magento\Framework\Model\AbstractModel|OrderItem $model
     * @return bool
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        if ($model instanceof OrderItem) {
            $product = $this->productRepository->getById($model->getProductId(), false, $model->getStoreId());
            $product->setQtyOrdered(
                $model->getQtyOrdered()
            )->setBasePrice(
                $this->searchValue($model, 'base_price')
            )->setBaseRowTotal(
                $this->searchValue($model, 'base_row_total')
            );

            // Validate by grandfather method
            // The parent method is not appropriate
            return AbstractProduct::validate($product);
        }

        return false;
    }

    /**
     * If necessary, gets value from parent Item
     *
     * @param \Magento\Framework\Model\AbstractModel|OrderItem $model
     * @param string $key
     *
     * @return mixed
     */
    protected function searchValue($model, $key)
    {
        if (!$model->getData($key) || $model->getData($key) == 0) {
            if ($model->hasData('parent_item')
                && $model->getParentItem() instanceof \Magento\Sales\Model\Order\Item
            ) {
                return $model->getParentItem()->getData($key);
            }
        }
        return $model->getData($key);
    }

    /**
     * @return $this
     */
    public function loadAttributeOptions()
    {
        $this->setAttributeOption($this->getAttributeOptionsArray());
        return $this;
    }

    /**
     * Get attributes for order items
     *
     * @return array
     */
    public function getAttributeOptionsArray()
    {
        return [
            'sku'            => __('Item sku'),
            'qty_ordered'    => __('Item quantity'),
            'base_price'     => __('Item price'),
            'base_row_total' => __('Item row total'),
        ];
    }
}
