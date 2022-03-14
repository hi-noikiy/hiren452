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
 * @package   mirasvit/module-product-action
 * @version   1.0.9
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);

namespace Mirasvit\ProductAction\Action\UpdatePrice;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\ProductAction\Api\ActionDataInterface;
use Mirasvit\ProductAction\Api\ExecutorInterface;

class Executor implements ExecutorInterface
{
    private $dateFilter;

    private $productRepository;

    private $storeManager;

    public function __construct(
        Date $dateFilter,
        ProductRepositoryInterface $productRepository,
        StoreManagerInterface $storeManager
    ) {
        $this->dateFilter        = $dateFilter;
        $this->productRepository = $productRepository;
        $this->storeManager      = $storeManager;
    }

    public function execute(ActionDataInterface $actionData): void
    {
        $actionData = $this->cast($actionData);

        foreach ($actionData->getIds() as $id) {
            /** @var Product $p */
            $p = $this->productRepository->getById($id, true, 0);

            if ($actionData->getPrice()) {
                $p->setPrice($this->getNewPrice($actionData->getPrice(), (float)$p->getPrice()));
            }

            if ($actionData->getCost()) {
                $p->setCost($this->getNewPrice($actionData->getCost(), (float)$p->getCost()));
            }

            if ($actionData->getSpecialPrice()) {
                $p->setSpecialPrice($this->getNewPrice($actionData->getSpecialPrice(), (float)$p->getSpecialPrice()));
            }

            if ($actionData->getSpecialPriceFromDate()) {
                $p->setSpecialFromDate($this->prepareDate($actionData->getSpecialPriceFromDate()));
            }

            if ($actionData->getSpecialPriceToDate()) {
                $p->setSpecialToDate($this->prepareDate($actionData->getSpecialPriceToDate()));
            }

            // we need this to save attributes to default storeview
            $this->storeManager->setCurrentStore(0);

            $this->productRepository->save($p);
        }
    }

    private function prepareDate(string $date): string
    {
        $data = [
            'value' => $date,
        ];

        $filterValues = [
            'value' => $this->dateFilter,
        ];

        $inputFilter = new \Zend_Filter_Input($filterValues, [], $data);
        $data        = $inputFilter->getUnescaped();

        return $data['value'];
    }

    private function cast(ActionDataInterface $class): ActionData
    {
        if ($class instanceof ActionData) {
            return $class;
        }

        throw new \InvalidArgumentException((string)__('Invalid class'));
    }

    private function getNewPrice(string $price, float $currentPrice): float
    {
        $price = trim($price);

        $isNegative = false;
        $isPercent  = false;
        $isPositive = false;

        // \p{Po} - because "%" has different presentation in unicode
        if (preg_match('/\p{Po}/u', $price[mb_strlen($price) - 1])) {
            $isPercent = true;
            $price     = mb_substr($price, 0, mb_strlen($price) - 1);
        }

        // \p{Po} - because "-" has different presentation in unicode
        if (preg_match('/\p{Pd}/u', $price)) {
            $isNegative = true;
            $price      = preg_replace('/\p{Pd}/u', '', $price);
        }

        if (strpos((string)$price, '+') !== false) {
            $isPositive = true;
            $price      = str_replace('+', '', $price);
        }

        if ($isPercent) {
            $price = $currentPrice * $price / 100;
        }

        if ($isNegative) {
            $price *= -1;
        }

        if ($isPositive || $isNegative) {
            $price = $currentPrice + $price;
        }

        return $price > 0 ? (float)$price : 0;
    }
}
