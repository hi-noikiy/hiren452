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



namespace Mirasvit\ProductKit\Service;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type\AbstractType;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\Tax\Helper\Data as TaxHelper;
use Mirasvit\Core\Service\SerializeService;
use Mirasvit\ProductKit\Api\Data\KitInterface;
use Mirasvit\ProductKit\Api\Data\KitItemInterface;
use Mirasvit\ProductKit\Data\OfferKitItem;
use Mirasvit\ProductKit\Model\ConfigProvider;
use Mirasvit\ProductKit\Repository\IndexRepository;
use Mirasvit\ProductKit\Repository\KitItemRepository;
use Mirasvit\ProductKit\Repository\KitRepository;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CartService
{
    private $indexRepository;

    private $taxHelper;

    private $kitItemRepository;

    private $kitRepository;

    private $serializer;

    public function __construct(
        IndexRepository $indexRepository,
        TaxHelper $taxHelper,
        KitItemRepository $kitItemRepository,
        KitRepository $kitRepository,
        Serializer $serializer
    ) {
        $this->indexRepository   = $indexRepository;
        $this->taxHelper         = $taxHelper;
        $this->kitItemRepository = $kitItemRepository;
        $this->kitRepository     = $kitRepository;
        $this->serializer        = $serializer;
    }

    /**
     * @param Quote $quote
     *
     * @return false|KitInterface
     */
    public function findSuitableProductKit(Quote $quote)
    {
        $allIds = [];
        foreach ($quote->getAllItems() as $item) {
            $allIds[] = $item->getProduct()->getId();
        }

        $collection = $this->kitRepository->getCollection();

        $collection
            ->addFilterByStoreId($quote->getStoreId())
            ->addFieldToFilter(KitInterface::IS_ACTIVE, 1)
            ->setOrder(KitInterface::PRIORITY);

        return false;
    }

    /**
     * @param OfferKitItem|KitItemInterface $kitItem
     * @param AbstractItem                  $item
     *
     * @return float|int
     */
    public function getDiscountAmount($kitItem, AbstractItem $item)
    {
        $discount     = .0;
        $percentTypes = [
            ConfigProvider::DISCOUNT_TYPE_PERCENTAGE,
            ConfigProvider::DISCOUNT_TYPE_PERCENTAGE_RELATIVE,
            ConfigProvider::DISCOUNT_TYPE_PERCENTAGE_KIT,
        ];

        if (in_array($kitItem->getDiscountType(), $percentTypes)) {
            $price = $item->getPriceInclTax();
            if ($this->taxHelper->displayCartPriceExclTax($item->getStore())) {
                $price = $item->getCalculationPrice();
            }

            $discount = $price * $item->getQty() * $kitItem->getDiscountAmount() / 100;
        } else {
            if ($kitItem->getDiscountType() == ConfigProvider::DISCOUNT_TYPE_FIXED) {
                $discount = $kitItem->getDiscountAmount();
            }
        }

        return $discount;
    }

    /**
     * @param int   $kitId
     * @param array $data Array of ['product_id' => id, 'item_id' => id, 'position' => number]
     *
     * @return array
     */
    public function getKitItems($kitId, $data)
    {
        $connection = $this->indexRepository->select()->getConnection();

        $indexQuery = $this->indexRepository->select();
        $indexQuery->columns('*');

        $where = [];
        foreach ($data as $row) {
            $where[] = '(index.product_id = ' . (int)$row['product_id'] .
                ' AND index.item_id = ' . (int)$row['item_id'] .
                ' AND index.position = ' . (int)$row['position'] .
                ')';
        }

        $indexQuery->where('(' . implode(' OR ', $where) . ')');
        $indexQuery->where('index.kit_id = ?', (int)$kitId);

        return $connection->fetchAssoc($indexQuery);
    }

    /**
     * @param int $kitId
     * @param int $productId
     * @param int $position
     *
     * @return int
     */
    public function findKitIndexByProduct($kitId, $productId, $position)
    {
        $connection = $this->indexRepository->select()->getConnection();

        $indexQuery = $this->indexRepository->select();
        $indexQuery->columns('item_id')
            ->where('index.kit_id = ?', (int)$kitId)
            ->where('index.product_id = ?', (int)$productId)
            ->where('index.position = ?', (int)$position);

        return (int)$connection->fetchOne($indexQuery);
    }

    /**
     * @param Product|AbstractType $product
     * @param array                $productData
     *
     * @return array
     */
    public function prepareProductService($product, $productData)
    {
        $item = $this->kitItemRepository->get($productData['item_id']);

        $productData['qty'] = $item->getQty();
        if ($productData['qty'] > 0) {
            $objectManager      = \Magento\Framework\App\ObjectManager::getInstance();
            $filter             = new \Zend_Filter_LocalizedToNormalized([
                'locale' => $objectManager->get(ResolverInterface::class)->getLocale(),
            ]);
            $productData['qty'] = $filter->filter((string)$productData['qty']);
        }

        $hash = $productData['hash'];

        $selectedCombination = $productData['selectedCombination'];

        if ($product->getTypeId() == 'grouped') {
            $products = $product->getTypeInstance()->getAssociatedProducts($product);
            foreach ($products as $groupedProduct) {
                $groupedProduct->addCustomOption(
                    'kit_info',
                    SerializeService::encode(
                        [
                            'kit_id'   => (int)$productData['kit_id'],
                            'kit_info' => [
                                'kit_id'              => (int)$productData['kit_id'],
                                'item_id'             => (int)$productData['item_id'],
                                'product_id'          => (int)$productData['product_id'],
                                'position'            => (int)$productData['position'],
                                'qty'                 => (float)$productData['qty'],
                                'hash'                => $hash,
                                'selectedCombination' => $selectedCombination,
                            ],
                        ]
                    )
                );
            }
        }
        $productData['kit_info'] = [
            'kit_id'              => (int)$productData['kit_id'],
            'item_id'             => (int)$productData['item_id'],
            'product_id'          => (int)$productData['product_id'],
            'position'            => (int)$productData['position'],
            'qty'                 => (float)$productData['qty'],
            'hash'                => $hash,
            'selectedCombination' => $selectedCombination,
        ];
        unset($productData['item_id']);

        return $productData;
    }

    /**
     * @param AbstractItem $item
     *
     * @return array|false
     */
    public function getItemOptions($item)
    {
        $result = false;

        $requestOptions = $this->getItemInfoOptions($item);
        if (!$requestOptions || !isset($requestOptions['kit_id']) || !isset($requestOptions['kit_info'])) {
            $requestOptions = $this->getGroupedItemOptions($item);
            if (!$requestOptions) {
                return $result;
            }
        }
        if (!isset($requestOptions['kit_info']['hash'])) {
            return $result;
        }

        return $requestOptions;
    }

    /**
     * @param AbstractItem $item
     *
     * @return array|false
     */
    public function getGroupedItemOptions($item)
    {
        $result = false;

        /** @var \Magento\Catalog\Model\Product\Configuration\Item\Option $options */
        $options = $item->getOptionByCode('kit_info');
        if (empty($options)) {
            return $result;
        }

        $requestOptions = $options->getValue();
        try {
            $requestOptions = $this->serializer->unserialize($requestOptions);
        } catch (\Exception $e) {
            return $result;
        }
        if (!$requestOptions || !isset($requestOptions['kit_id']) || !isset($requestOptions['kit_info'])) {
            return $result;
        }

        return $requestOptions;
    }

    /**
     * @param AbstractItem $item
     *
     * @return array|false
     */
    public function getItemInfoOptions($item)
    {
        $result = false;

        /** @var \Magento\Catalog\Model\Product\Configuration\Item\Option $options */
        $options = $item->getOptionByCode('info_buyRequest');
        if (empty($options)) {
            return $result;
        }

        $requestOptions = $options->getValue();
        try {
            $requestOptions = $this->serializer->unserialize($requestOptions);
        } catch (\Exception $e) {
            return $result;
        }

        return $requestOptions;
    }
}
