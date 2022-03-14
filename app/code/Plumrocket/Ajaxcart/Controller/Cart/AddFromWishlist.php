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
 * @package     Plumrocket Ajaxcart v2.x.x
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Ajaxcart\Controller\Cart;

class AddFromWishlist extends \Plumrocket\Ajaxcart\Controller\AbstractCart
{
    /**
     * Execute AddFromWishlist action
     *
     * @return \Magento\Framework\Controller\Result\JsonFactory;
     */
    public function execute()
    {
        $itemId = (int) $this->getRequest()->getParam('item');

        /* @var $item \Magento\Wishlist\Model\Item */
        $item = $this->wishlistItemFactory->create()->load($itemId);
        if (!$item->getId()) {
            $this->messageManager->addException(new \Exception, __('Cannot add the item to shopping cart.'));
            return $this->sendResponse();
        }
        $wishlist = $this->getWishlist($item->getWishlistId());

        if (!$wishlist) {
            return $this->sendResponse();
        }

        // Set qty
        $qty = ( $this->getRequest()->getParam('qty') ) ? $this->getRequest()->getParam('qty') : $item->getQty();
        if (is_array($qty)) {
            if (isset($qty[$itemId])) {
                $qty = $qty[$itemId];
            } else {
                $qty = 1;
            }
        }

        if (!$qty || $qty < 1) {
            $qty = 1;
        }
        $qty = $this->quantityProcessor->process($qty);
        if ($qty) {
            $item->setQty($qty);
        }

        /* @var \Magento\Checkout\Model\Cart */
        $cart = $this->cart;

        try {
            $options = $this->wishlistOptionCollection->create()
                    ->addItemFilter([$itemId]);

            $item->setOptions($options->getOptionsByItem($itemId));

            if (empty(!$this->checkWishListItem($item))) {
                $this->_forward('addconfigure', null, null, ['product' => $item->getProductId(), 'wishlist' => $itemId, 'qty' => $qty]);
                return;
            }

            $buyRequest = $this->productHelper->addParamsToBuyRequest(
                $this->getRequest()->getParams(),
                ['current_config' => $item->getBuyRequest()]
            );

            $item->mergeBuyRequest($buyRequest);
            if ($item->addToCart($cart, true)) {
                $cart->save()->getQuote()->collectTotals();
            }

            $wishlist->save();
            $this->wishlistHelper->calculate();

        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->_forward('addconfigure', null, null, ['product' => $item->getProductId(), 'wishlist' => $itemId, 'qty' => $qty]);
            return;
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            $this->messageManager->addException($e, __('Cannot add item to shopping cart'));
            return $this->sendResponse();
        }

        $this->wishlistHelper->calculate();

        $product = $item->getProduct();
        return $this->afterAdd($cart, $product);
    }

    /**
     * @param $item
     * @return bool
     */
    protected function checkWishListItem($item)
    {
        $product = $item->getProduct();
        if (!$product->getId()) {
            return false;
        }

        if  ($this->isConfigure($product) && !$item->isRepresent($product, $item->getBuyRequest())) {
            return false;
        }

        $attributes = $item->getBuyRequest()->getSuperAttribute();

        if (is_array($attributes)) {
            foreach ($attributes as $key => $val) {
                if (empty($val)) {
                    unset($attributes[$key]);
                }
            }

            if (empty($attributes)) {
                return false;
            }
        }

        return true;
    }
}
