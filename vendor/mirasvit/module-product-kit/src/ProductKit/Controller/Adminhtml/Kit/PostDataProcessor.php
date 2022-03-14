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



namespace Mirasvit\ProductKit\Controller\Adminhtml\Kit;

use Mirasvit\Core\Service\SerializeService;
use Mirasvit\ProductKit\Api\Data\KitInterface;
use Mirasvit\ProductKit\Api\Data\KitItemInterface;
use Mirasvit\ProductKit\Repository\KitRepository;
use Mirasvit\ProductKit\Ui\Kit\Form\Modifier\SmartItemModifier;

class PostDataProcessor
{
    private $kitRepository;

    public function __construct(
        KitRepository $kitRepository
    ) {
        $this->kitRepository = $kitRepository;
    }

    public function filterPostData(array $data)
    {
        if (isset($data[SmartItemModifier::SMART_ITEM])) {
            $smartItemsData = $data[SmartItemModifier::SMART_ITEM];
            unset($data[SmartItemModifier::SMART_ITEM]);

            $smartItems = [];

            $itemsAmount = KitInterface::SMART_BLOCKS_DEFAULT;
            if (!empty($data['smart_items_number'])) {
                $itemsAmount = (int)$data['smart_items_number'];
            }
            for ($i = 1; $i <= $itemsAmount; $i++) {
                if (isset($smartItemsData[$i]) && $smartItemsData[$i][KitItemInterface::QTY] > 0) {
                    $smartItems[] = $smartItemsData[$i];
                }
            }

            $data['items'] = $smartItems;
        }

        return $data;
    }

    public function setData(KitInterface $kit, array $data)
    {
        foreach ($data as $key => $value) {
            $kit->setDataUsingMethod($key, $value);
        }

        return $kit;
    }

    public function getKitItems(KitInterface $kit, array $data)
    {
        if ($kit->isSmart()) {
            $items = $this->createSmartItems($kit, $data);
        } else {
            $items = $this->createRegularItems($kit, $data);
        }

        return $items;
    }

    private function createSmartItems(KitInterface $kit, array $data)
    {
        $itemRepository = $this->kitRepository->getItemRepository();

        $items = [];
        foreach ($data['items'] as $k => $itemData) {
            $item = $itemRepository->create();

            if (isset($itemData[KitItemInterface::CONDITIONS])) {
                $conditions = $item->getRule()->buildPostSmartConditions([$itemData[KitItemInterface::CONDITIONS]]);

                $itemData[KitItemInterface::CONDITIONS] = SerializeService::encode($conditions[0]);
            }

            foreach ($itemData as $key => $value) {
                $item->setDataUsingMethod($key, $value);
            }

            $item->setKitId($kit->getId());
            $item->setProductId(0);

            if (!$item->getId()) {
                $item->setTmpId('tmp_' . $k);
            }

            $items[$item->getPosition()] = $item;
        }

        return $items;
    }

    private function createRegularItems(KitInterface $kit, array $data)
    {
        if (!isset($data['links']) && !isset($data['links']['product_kit_kit_form_product_listing'])) {
            $data['links']['product_kit_kit_form_product_listing'] = [];
        }

        $itemRepository = $this->kitRepository->getItemRepository();

        $items = [];

        foreach ($data['links']['product_kit_kit_form_product_listing'] as $k => $itemData) {
            $item = $itemRepository->create();

            $item->setKitId($kit->getId())
                ->setProductId($itemData['id'])
                ->setPosition($itemData[KitItemInterface::POSITION])
                ->setIsOptional($itemData[KitItemInterface::IS_OPTIONAL])
                ->setIsPrimary($itemData[KitItemInterface::IS_PRIMARY])
                ->setQty($itemData[KitItemInterface::QTY])
                ->setDiscountAmount($itemData[KitItemInterface::DISCOUNT_AMOUNT])
                ->setDiscountType($itemData[KitItemInterface::DISCOUNT_TYPE]);

            if (!$item->getId()) {
                $item->setTmpId('tmp_' . $k);
            }

            $items[$item->getPosition()] = $item;
        }

        return $items;
    }
}
