<?php

namespace Meetanshi\Partialpro\Plugin\Checkout\Controller;

use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\UrlFactory;
use Meetanshi\Partialpro\Helper\Data;
use Magento\Checkout\Controller\Index\Index;
use Magento\Checkout\Model\Cart;

class Loginrequired
{
    private $urlModel;
    private $helper;
    private $resultRedirectFactory;
    private $messageManager;
    private $cart;

    public function __construct(
        UrlFactory $urlFactory,
        Data $helper,
        RedirectFactory $redirectFactory,
        ManagerInterface $messageManager,
        Cart $cart
    )
    {
        $this->urlModel = $urlFactory;
        $this->resultRedirectFactory = $redirectFactory;
        $this->helper = $helper;
        $this->messageManager = $messageManager;
        $this->cart = $cart;
    }

    public function aroundExecute(
        Index $subject,
        \Closure $proceed
    )
    {
        $this->urlModel = $this->urlModel->create();
        if ($this->helper->isModuleEnabled()) {
            $quoteId = $this->cart->getQuote()->getId();
            if ($this->helper->isLoginRequired() && $this->helper->getPartialProductSet($quoteId) && !($this->helper->getCustomerLogin())) {

                $this->messageManager->addErrorMessage(__('Please login to place order using the Partial Payment facility.'));
                $defaultUrl = $this->urlModel->getUrl('customer/account/', ['_secure' => true]);
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setUrl($defaultUrl);

            } else if ($this->helper->isLoginRequired() && $this->helper->getCustomerLogin() && $this->helper->getPartialProductSet($quoteId)) {

                $customerId = $this->helper->getCustomerId();
                if ($customerId) {

                    $grandTotal = $this->cart->getQuote()->getGrandTotal();
                    $creditLimit = $this->helper->getCreditLimit();
                    $creditLimitErrorMsg = $this->helper->getCreditLimitErrorMsg();
                    $lifeTimePartialOrderPrice = $this->helper->getLifetimePartialOrderPrice() + $grandTotal;


                    if ($lifeTimePartialOrderPrice > $creditLimit) {
                        $this->messageManager->addErrorMessage(__($creditLimitErrorMsg));
                        $defaultUrl = $this->urlModel->getUrl('checkout/cart/', ['_secure' => true]);
                        $resultRedirect = $this->resultRedirectFactory->create();
                        return $resultRedirect->setUrl($defaultUrl);
                    }
                }
            }
        }
        return $proceed();
    }
}