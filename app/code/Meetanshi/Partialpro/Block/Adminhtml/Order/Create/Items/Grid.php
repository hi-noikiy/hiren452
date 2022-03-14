<?php

namespace Meetanshi\Partialpro\Block\Adminhtml\Order\Create\Items;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Model\Session\Quote;
use Magento\Sales\Model\AdminOrder\Create;
use Magento\Wishlist\Model\WishlistFactory;
use Magento\GiftMessage\Model\Save;
use Magento\Tax\Model\Config;
use Magento\Tax\Helper\Data;
use Magento\GiftMessage\Helper\Message;
use Magento\Catalog\Model\ProductFactory;
use Magento\Checkout\Model\Session;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Api\StockStateInterface;
use Meetanshi\Partialpro\Helper\Data as partialHelper;
use Magento\Sales\Block\Adminhtml\Order\Create\Items\Grid as salesGrid;
use Magento\Framework\Serialize\SerializerInterface;


class Grid extends salesGrid
{
    protected $helper;
    protected $productModel;
    protected $checkoutSession;
    protected $customerFactory;
    protected $objectManager;
    private $serializer;


    public function __construct(
        Context $context,
        Quote $sessionQuote,
        Create $orderCreate,
        WishlistFactory $wishlistFactory,
        Save $giftMessageSave,
        Config $taxConfig,
        Data $taxData,
        Message $messageHelper,
        ProductFactory $productModel,
        Session $checkoutSession,
        CustomerFactory $customerFactory,
        ObjectManagerInterface $objectManager,
        PriceCurrencyInterface $priceCurrency,
        StockRegistryInterface $stockRegistry,
        StockStateInterface $stockState,
        partialHelper $data_helper,
        SerializerInterface $serializer,
        array $data = []
    )
    {
        $this->helper = $data_helper;
        $this->productModel = $productModel;
        $this->checkoutSession = $checkoutSession;
        $this->customerFactory = $customerFactory;
        $this->objectManager = $objectManager;
        $this->serializer = $serializer;
        parent::__construct($context, $sessionQuote, $orderCreate, $priceCurrency, $wishlistFactory, $giftMessageSave, $taxConfig, $taxData, $messageHelper, $stockRegistry, $stockState, $data);
    }

    public function showPartialPayment()
    {
        $customerGroupId = $this->customerFactory->create()->load($this->getCustomerId())->getGroupId();
        $isValidCustomer = $this->helper->isValidCustomer($customerGroupId);
        $partialEnable = $this->helper->isModuleEnabled();
        if ($isValidCustomer && $partialEnable) {
            return 1;
        }
        return 0;
    }

    public function canShowPrtialPaymentOnProduct()
    {
        if ($this->getApplyPartialPaymentToWhole()) {
            return 0;
        }
        return 1;
    }

    public function getApplyPartialPaymentToWhole()
    {
        $val = $this->helper->getApplyPartialPaymentToWhole();
        if ($val == 2) {
            return 1;
        } else {
            return 0;
        }
    }

    public function getInstallmentCountWhole()
    {
        return $this->helper->getInstallmentNumber();
    }

    public function getInstallmentCount($productId)
    {
        $product = $this->productModel->create()->load($productId);
        if ($product->getId()) {
            $applyPartialPamment = $product->getData('apply_partial_payment');
            if ($applyPartialPamment) {
                $installmentNumber = $product->getData('no_installment');
            } else {
                $installmentNumber = $this->helper->getInstallmentNumber();
            }

            return $installmentNumber;
        }
        return 0;

    }

    public function getIsFlexyLaywayPlan()
    {
        return $this->helper->getIsFlexyLaywayPlan();
    }

    public function getProductpageLabel()
    {
        return $this->helper->getProductpageLabel();
    }

    public function getunserializeData($option)
    {
        return $this->serializer->unserialize($option);
    }
}