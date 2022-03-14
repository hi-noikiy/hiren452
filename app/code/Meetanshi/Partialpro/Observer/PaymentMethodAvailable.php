<?php

namespace Meetanshi\Partialpro\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Checkout\Model\Cart;
use Meetanshi\Partialpro\Helper\Data;
use Magento\Framework\App\State;
use Magento\Backend\Model\Session\Quote;

class PaymentMethodAvailable implements ObserverInterface
{
    protected $cart;
    protected $helperData;
    protected $state;
    protected $backendQutoe;

    const AREA_CODE = \Magento\Framework\App\Area::AREA_ADMINHTML;

    public function __construct(Cart $cart, Data $helperData, State $state, Quote $backendQutoe)
    {
        $this->cart = $cart;
        $this->helperData = $helperData;
        $this->state = $state;
        $this->backendQutoe = $backendQutoe;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            if ($this->helperData->isModuleEnabled()) {

                if (!$this->helperData->getAllowedPaymentMethod($observer->getEvent()->getMethodInstance()->getCode())) {

                    $isPartialProductInCart = 0;
                    $areaCode = $this->state->getAreaCode();
                    if ($areaCode == self::AREA_CODE) {
                        $cartItemsAll = $this->backendQutoe->getQuote()->getAllItems();
                    } else {
                        $cartItemsAll = $this->cart->getQuote()->getAllItems();
                    }

                    foreach ($cartItemsAll as $item) {
                        if ($item->getPartialApply()) {
                            $isPartialProductInCart = 1;
                        }
                    }
                    if ($isPartialProductInCart) {
                        $checkResult = $observer->getEvent()->getResult();
                        $checkResult->setData('is_available', false);
                    }
                }
            }
        } catch (\Exception $e) {
            \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->info($e->getMessage());
        }
    }
}
