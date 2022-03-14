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

use Magento\Framework\Exception\LocalizedException;
use Magento\Catalog\Model\Product\Type as ProductType;

class Add extends \Plumrocket\Ajaxcart\Controller\AbstractCart
{
    protected $blocksHtml = null;

    /**
     * Add product to shopping cart action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $request = $this->getRequest();

        if (!$this->_formKeyValidator->validate($request)) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        $cart   = $this->cart;
        $params = $request->getParams();
        $session = $this->_checkoutSession;

        file_put_contents(BP . '/var/log/eeeajax.log', print_r($params, true) . PHP_EOL, FILE_APPEND);


        try {
            if (isset($params['qty'])) {
                $filter = new \Zend_Filter_LocalizedToNormalized(
                    ['locale' => $this->localeResolver->getLocale()]
                );
                $params['qty'] = $filter->filter($params['qty']);
            }

            $product = $this->initProduct();

            $related = $request->getParam('related_product');

            if (!$product) {
                $session->addSuccess(__('Product not longer exists.'));
                return $this->sendResponse();
            }

            if (!$this->canAdd($product)) {
                return $this->redirectToProductPage($product->getProductUrl());
            }

            if (
                $product->getTypeId() == ProductType::TYPE_BUNDLE
                && empty($this->_request->getParam('bundle_option'))
            ) {
                $this->_forward('addconfigure');
                return;
            }

            $cart->addProduct($product, $params);
            if (!empty($related)) {
                $cart->addProductsByIds(explode(',', $related));
            }

            $cart->save();

            $session->setCartWasUpdated(true);

            $this->_eventManager->dispatch(
                'checkout_cart_add_product_complete',
                [
                    'product' => $product,
                    'request' => $request,
                    'response' => $this->getResponse()
                ]
            );

            //TODO: Discover this
            $this->addedQty = 0;
            foreach($this->dataHelper->getAddedQuoteItems() as $item) {
                $this->addedQty += $item->getQtyToAdd();
            };

            if (!$session->getNoCartRedirect(true)) {
                return $this->afterAdd($cart, $product);
            }
        } catch (LocalizedException $e) {
            if ($session->getUseNotice(true) || $product->hasOptions()) {
                $this->messageManager->addNotice($this->escaper->escapeHtml($e->getMessage()));
                $this->_forward('addconfigure');
                return;
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $this->messageManager->addError($this->escaper->escapeHtml($message));
                }
            }

            return $this->sendResponse();
        } catch (\Exception $e) {
            $this->messageManager->addException($e, $e->getMessage());
            $this->logger->critical($e);
            return $this->sendResponse();
        }
    }
}
