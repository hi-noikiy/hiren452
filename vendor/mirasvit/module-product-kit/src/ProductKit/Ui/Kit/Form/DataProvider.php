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



namespace Mirasvit\ProductKit\Ui\Kit\Form;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Mirasvit\ProductKit\Api\Data\KitInterface;
use Mirasvit\ProductKit\Model\ConfigProvider;
use Mirasvit\ProductKit\Repository\KitItemRepository;
use Mirasvit\ProductKit\Repository\KitRepository;

class DataProvider extends AbstractDataProvider
{
    private $kitRepository;

    private $kitItemRepository;

    private $itemModifier;

    private $smartItemModifier;

    /**
     * DataProvider constructor. DO NOT change "mixed"
     * @param KitRepository $kitRepository
     * @param KitItemRepository $kitItemRepository
     * @param Modifier\ItemModifier $itemModifier
     * @param Modifier\SmartItemModifier $smartItemModifier
     * @param mixed $name
     * @param mixed $primaryFieldName
     * @param mixed $requestFieldName
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        KitRepository $kitRepository,
        KitItemRepository $kitItemRepository,
        Modifier\ItemModifier $itemModifier,
        Modifier\SmartItemModifier $smartItemModifier,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        $this->kitRepository     = $kitRepository;
        $this->kitItemRepository = $kitItemRepository;
        $this->itemModifier      = $itemModifier;
        $this->smartItemModifier = $smartItemModifier;

        $this->collection = $this->kitRepository->getCollection();

        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getMeta()
    {
        $meta = parent::getMeta();

        $meta = $this->smartItemModifier->modifyMeta($meta);
        $meta = $this->itemModifier->modifyMeta($meta);

        return $meta;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $result = [];

        foreach ($this->collection as $model) {
            $data = $model->getData();

            $data[KitInterface::STORE_IDS]          = $model->getStoreIds();
            $data[KitInterface::CUSTOMER_GROUP_IDS] = $model->getCustomerGroupIds();

            $data['general_discount_type'] = $this->getGeneralDiscountType($model);

            if ($model->isSmart()) {
                $data = $this->smartItemModifier->modifyData($model, $data);
            } else {
                $data = $this->itemModifier->modifyData($model, $data);
            }

            $result[$model->getId()] = $data;
        }
        if (!$this->collection->count()) {
            $result[''] = [
                KitInterface::IS_SMART            => 0,
                KitInterface::SMART_BLOCKS_AMOUNT => 0,
            ];
        }

        return $result;
    }

    private function getGeneralDiscountType(KitInterface $kit)
    {
        $types = [];
        $items = $this->kitItemRepository->getItems($kit);

        foreach ($items as $item) {
            $types[] = $item->getDiscountType();
        }

        $types = array_unique($types);

        return count($types) == 1 ? $types[0] : ConfigProvider::DISCOUNT_TYPE_COMPLEX;
    }
}
