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

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Registry;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;

class AreaContextService
{
    private $request;

    private $checkoutSession;

    private $customerSession;

    private $orderCollectionFactory;

    private $registry;

    public function __construct(
        RequestInterface $request,
        CheckoutSession $checkoutSession,
        CustomerSession $customerSession,
        OrderCollectionFactory $orderCollectionFactory,
        Registry $registry
    ) {
        $this->request                = $request;
        $this->checkoutSession        = $checkoutSession;
        $this->customerSession        = $customerSession;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->registry               = $registry;
    }

    /**
     * @param string $attributeCode
     *
     * @return array|bool
     */
    public function getAttributeValue($attributeCode)
    {
        $products = $this->findProducts();
        $category = $this->findCategory();

        if (!$products) {
            return false;
        }

        if ($attributeCode == 'category_ids') {
            if ($category) {
                return [$category->getId()];
            }

            return $products[0]->getCategoryIds();
        }

        $values = [];
        foreach ($products as $product) {
            $values[] = $product->getData($attributeCode);
        }

        return $values;
    }

    /**
     * @return Product[]
     */
    private function findProducts()
    {
        if ($this->request->getFullActionName() === 'checkout_cart_index') {
            $quote = $this->checkoutSession->getQuote();

            if (!$quote->getId()) {
                return [];
            }

            $products = [];
            foreach ($quote->getAllVisibleItems() as $item) {
                $products[] = $item->getProduct();
            }

            return $products;
        }

        if ($this->registry->registry('current_product')) {
            return [$this->registry->registry('current_product')];
        }

        return [];
    }

    /**
     * @return Category|false
     */
    private function findCategory()
    {
        return $this->registry->registry('current_category');
    }
}
