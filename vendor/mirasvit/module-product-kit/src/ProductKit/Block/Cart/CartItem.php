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



namespace Mirasvit\ProductKit\Block\Cart;

use Magento\Catalog\Block\Product\View;
use Magento\Catalog\Model\Product;

class CartItem extends View
{
    private $error = '';

    public function _construct()
    {
        $this->setTemplate('Mirasvit_ProductKit::cart/cart-item.phtml');

        parent::_construct();
    }

    public function getOptions()
    {
        $prefix = '';
        $suffix = '';

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Magento\Framework\Module\Manager $moduleManager */
        $moduleManager = $objectManager->get(\Magento\Framework\Module\Manager::class);
        if ($moduleManager->isEnabled('Codazon_GoogleAmpManager') &&
            $this->getProduct()->getTypeId() == 'grouped'
        ) {
            $block = $this->getLayout()->getBlock('product.info.grouped');
            if (!$block) {
                $block = $this->getLayout()->createBlock(View::class, 'product.info.grouped', [
                    'data' => [],
                ]);
            }

            $prefix = '<form>';
            $suffix = '<input type="hidden" name="product" value="' . $this->getProduct()->getId() . '"/></form>';
        } elseif ($moduleManager->isEnabled('Codazon_GoogleAmpManager') &&
            $this->getProduct()->getTypeId() == 'configurable'
        ) {
            $block = $this->getLayout()->getBlock('product.info.addtocart_wrap');
            if (!$block) {
                $block = $this->getLayout()->createBlock(View::class, 'product.info.addtocart_wrap', [
                    'data' => [],
                ]);
            }

            $prefix = '<form>';
            $suffix = '<input type="hidden" name="product" value="' . $this->getProduct()->getId() . '"/></form>';

            $this->getLayout()->unsetElement('product.info.estimated_delivery_time_mageworx');
            $this->getLayout()->unsetElement('product.info.estimated_delivery_time_mageworx.additional');
            $this->getLayout()->unsetElement('shipping-estimation-configurable');
        } else {
            $block = $this->getLayout()->getBlock('product.info');
            if (!$block) {
                $block = $this->getLayout()->createBlock(View::class, 'product.info', [
                    'data' => [],
                ]);
            }
        }

        $this->getLayout()->unsetElement('product.info.options.wrapper.bottom');
        $this->getLayout()->unsetElement('aw_sbb.product.before_product_options');
        $this->getLayout()->unsetElement('aw_sbb.product.before_product_options.form');
        $this->getLayout()->unsetElement('aw_sbb.product.before_product_options.options');

        $block->setProduct($this->getProduct());

        return $prefix . $block->toHtml() . $suffix;
    }

    /**
     * @param array $error
     *
     * @return $this
     */
    public function setError($error)
    {
        $this->error = $error;

        return $this;
    }

    public function getError()
    {
        return $this->error;
    }

    /**
     * @param Product $product
     * @param string $imageId
     * @return string
     */
    public function getImageUrl(Product $product, $imageId = '')
    {
        // we use ObjectManager for compatibility with different Magento versions
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Magento\Catalog\Helper\Image $imageHelper */
        $imageHelper = $objectManager->get('Magento\Catalog\Helper\Image');

        return $imageHelper->init($product, $imageId)->getUrl();
    }
}
