<?php

namespace Meetanshi\Partialpro\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\Checkout\Model\Cart;
use Magento\Quote\Model\QuoteFactory;
use Magento\Framework\App\Helper\Context;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Directory\Model\Currency;
use Meetanshi\Partialpro\Model\ResourceModel\Partialpayment\CollectionFactory as partialpaymentCollection;
use Meetanshi\Partialpro\Model\ResourceModel\Installments\CollectionFactory as installmentCollection;
use Magento\Framework\Session\SessionManager;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Quote\Model\Quote\ItemFactory;
use Magento\Cms\Model\Template\FilterProvider;

/**
 * Class Data
 * @package Meetanshi\Partialpro\Helper
 */
class Data extends AbstractHelper
{
    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var FilterProvider
     */
    protected $filterProvider;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;
    /**
     * @var QuoteFactory
     */
    protected $quoteFactory;
    /**
     * @var Cart
     */
    public $cart;
    /**
     * @var SessionFactory
     */
    protected $customer;
    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrencyInterface;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManagerInterface;
    /**
     * @var Currency
     */
    protected $currency;
    /**
     * @var partialpaymentCollection
     */
    protected $partialPaymentCollection;
    /**
     * @var installmentCollection
     */
    protected $installmentCollection;
    /**
     * @var SessionManager
     */
    protected $sessionManager;
    /**
     * @var EncryptorInterface
     */
    protected $encryptor;

    /**
     * @var ItemFactory
     */
    protected $quoteItemFactory;

    /**
     *
     */
    const PARTIAL_ENABLE = 'partialpro/general/enable';
    /**
     *
     */
    const PARTIAL_INCLUDE_SHIPPING_TEX = 'partialpro/general/include_shipping_tex';
    /**
     *
     */
    const PARTIAL_FREQUENCY = 'partialpro/calculation/frequency';
    /**
     *
     */
    const APPLY_PARTIAL_PAYMENT_TO = 'partialpro/general/apply_methods';
    /**
     *
     */
    const ALLOWED_CUSTOMER_GRP = 'partialpro/general/customer_group';
    /**
     *
     */
    const ALLOWED_PAYMENT_METHOD = 'partialpro/general/payment_methods';
    /**
     *
     */
    const LOGIN_REQUIRED = 'partialpro/general/disable_guest';

    /**
     *
     */
    const CONFIG_CUSTOM_IS_ENABLED = 'partialpro/calculation/installment_fee_apply_on';
    /**
     *
     */
    const CONFIG_CUSTOM_FEE = 'partialpro/calculation/installment_fee';
    /**
     *
     */
    const CONFIG_FEE_LABEL = 'partialpro/calculation/installment_fee_label';
    /**
     *
     */
    const CONFIG_FEE_CALCULATION = 'partialpro/calculation/installment_calculation_fee_on';

    /**
     *
     */
    const DOWN_PAYMENT_TEX = 'partialpro/calculation/down_payment_text';
    /**
     *
     */
    const PAYING_NOW_LATER_TEXT = 'partialpro/calculation/pay_later_text';
    /**
     *
     */
    const DAYS_COUNT = 'partialpro/calculation/frequency_days';
    /**
     *
     */
    const WHOLE_CART_MINIMUM_AMOUNT = 'partialpro/general/min_amount';
    /**
     *
     */
    const WHOLE_CART_MINIMUM_ERR_MSG = 'partialpro/general/min_amount_err_msg';

    /**
     *
     */
    const PRODUCTPAGE_PAGELABEL = 'partialpro/productpage/pagelabel';
    /**
     *
     */
    const PRODUCTPAGE_DESCRIPTION = 'partialpro/productpage/description';

    /**
     *
     */
    const NO_INSTALLMENT = 'partialpro/calculation/no_installment';
    /**
     *
     */
    const DOWN_PAYMENT = 'partialpro/calculation/down_payment';
    /**
     *
     */
    const DOWN_PAYMENT_CALCULATION = 'partialpro/calculation/down_payment_calculation';
    /**
     *
     */
    const PAYMENT_TYPE = 'partialpro/calculation/payment_type';

    /**
     *
     */
    const PAY_ALL_INSTALLMENTS = 'partialpro/general/payall';
    /**
     *
     */
    const AUTO_CAPTURE = 'partialpro/general/autocapture';
    /**
     *
     */
    const AUTO_CAPTURE_REMINDER_DAYS = 'partialpro/email/autocapture/days';
    /**
     *
     */
    const REMINDER_DAYS = 'partialpro/email/reminder/days';

    /**
     *
     */
    const CREDIT_LIMIT = 'partialpro/general/creditlimit';
    /**
     *
     */
    const CREDIT_LIMIT_ERROR_MSG = 'partialpro/general/creditlimiterror';

    /**
     *
     */
    const CONFIG_ORANGE_ACTIVE = 'payment/orangeivory/active';
    /**
     *
     */
    const CONFIG_ORANGE_MODE = 'payment/orangeivory/mode';

    /**
     *
     */
    const CONFIG_ORANGE_SANDBOX_INIT_URL = 'payment/orangeivory/sandbox_init_url';
    /**
     *
     */
    const CONFIG_ORANGE_LIVE_INIT_URL = 'payment/orangeivory/live_init_url';

    /**
     *
     */
    const CONFIG_ORANGE_SANDBOX_GATEWAY_URL = 'payment/orangeivory/sandbox_gateway_url';
    /**
     *
     */
    const CONFIG_ORANGE_LIVE_GATEWAY_URL = 'payment/orangeivory/live_gateway_url';

    /**
     *
     */
    const CONFIG_ORANGE_INSTRUCTIONS = 'payment/orangeivory/instructions';
    /**
     *
     */
    const CONFIG_ORANGE_SANDBOX_MERCHANT_ID = 'payment/orangeivory/sandbox_merchant_id';
    /**
     *
     */
    const CONFIG_ORANGE_LIVE_MERCHANT_ID = 'payment/orangeivory/live_merchant_id';
    /**
     *
     */
    const CONFIG_ORANGE_LOGO = 'payment/orangeivory/show_logo';

    /**
     * Data constructor.
     * @param Context $context
     * @param ProductFactory $productFactory
     * @param ProductRepositoryInterface $productRepository
     * @param QuoteFactory $quoteFactory
     * @param SessionFactory $customer
     * @param PriceCurrencyInterface $priceCurrencyInterface
     * @param StoreManagerInterface $storeManagerInterface
     * @param Currency $currency
     * @param Cart $cart
     * @param partialpaymentCollection $partialPaymentCollection
     * @param installmentCollection $installmentCollection
     * @param SessionManager $sessionManager
     * @param EncryptorInterface $encryptor
     */
    public function __construct(
        Context $context,
        ProductFactory $productFactory,
        ProductRepositoryInterface $productRepository,
        QuoteFactory $quoteFactory,
        SessionFactory $customer,
        PriceCurrencyInterface $priceCurrencyInterface,
        StoreManagerInterface $storeManagerInterface,
        Currency $currency,
        Cart $cart,
        partialpaymentCollection $partialPaymentCollection,
        installmentCollection $installmentCollection,
        SessionManager $sessionManager,
        ItemFactory $quoteItemFactory,
        EncryptorInterface $encryptor,
        FilterProvider $filterProvider
    )
    {
        parent::__construct($context);
        $this->productFactory = $productFactory;
        $this->productRepository = $productRepository;
        $this->quoteFactory = $quoteFactory;
        $this->cart = $cart;
        $this->customer = $customer;
        $this->priceCurrencyInterface = $priceCurrencyInterface;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->currency = $currency;
        $this->partialPaymentCollection = $partialPaymentCollection;
        $this->installmentCollection = $installmentCollection;
        $this->sessionManager = $sessionManager;
        $this->encryptor = $encryptor;
        $this->quoteItemFactory = $quoteItemFactory;
        $this->filterProvider = $filterProvider;
    }

    /**
     * @return mixed
     */
    public function isModuleEnabled()
    {
        return $this->scopeConfig->getValue(self::PARTIAL_ENABLE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return int
     */
    public function getIsFlexyLaywayPlan()
    {
        if ($this->scopeConfig->getValue(self::PAYMENT_TYPE, ScopeInterface::SCOPE_STORE) == 2) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * @return mixed
     */
    public function getInstallmentNumber()
    {
        return $this->scopeConfig->getValue(self::NO_INSTALLMENT, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getPayAllInstallments()
    {
        return $this->scopeConfig->getValue(self::PAY_ALL_INSTALLMENTS, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getAutoCapture()
    {
        return $this->scopeConfig->getValue(self::AUTO_CAPTURE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getReminderDays()
    {
        return $this->scopeConfig->getValue(self::REMINDER_DAYS, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getAutoCaptureReminderDays()
    {
        return $this->scopeConfig->getValue(self::AUTO_CAPTURE_REMINDER_DAYS, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getConfigDownPayment()
    {
        return $this->scopeConfig->getValue(self::DOWN_PAYMENT, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getConfigDownCalculation()
    {
        return $this->scopeConfig->getValue(self::DOWN_PAYMENT_CALCULATION, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param $productId
     * @param $installmentNumber
     * @return string
     */
    public function getInstallmentTable($productId, $installmentNumber)
    {
        $firstInstallmentPrice = $otherInstallmentPrice = $installmentFee = $remainingPaymentPrice = $downPaymentPrice = 0;
        $currencyCode = $this->priceCurrencyInterface->getCurrency()->getCurrencyCode();
        $todayDate = date('Y-m-d');

        $product = $this->productFactory->create()->load($productId);
        $mainProductPrice = $product->getFinalPrice();
        $finalPrice = $this->convertPrice($mainProductPrice);

        $configFee = $this->getConfigInstallmentFee();
        $configFeeCalculation = $this->getInstallmentFeeCalculation();
        $configDownPayment = $this->getConfigDownPayment();
        $configDownCalculation = $this->getConfigDownCalculation();

        if ($product->getDownPayment() && $product->getCalculationDownPayment() && $product->getApplyPartialPayment()) {
            $downPayment = $product->getDownPayment();
            $downCalculation = $product->getCalculationDownPayment();
        } else {
            $downPayment = $configDownPayment;
            $downCalculation = $configDownCalculation;
        }

        if ($downCalculation == 2) {
            $firstInstallmentPrice = $finalPrice * $downPayment / 100;
        } else {
            $firstInstallmentPrice = $downPayment;
        }
        $remainingPaymentPrice = $finalPrice - $firstInstallmentPrice;

        if ($product->getInstallmentFee() && $product->getCalInstammentFeePayment() && $product->getApplyPartialPayment()) {
            $installmentFee = $product->getInstallmentFee();
            $calculateInstallmentFeePayment = $product->getCalInstammentFeePayment();
        } else {
            $installmentFee = $configFee;
            $calculateInstallmentFeePayment = $configFeeCalculation;
        }

        if ($calculateInstallmentFeePayment == 2) {
            $installmentFee = ($mainProductPrice * $installmentFee) / 100;
        }

        $installmentFee = $this->convertPrice($installmentFee);

        if ($this->getPartialInstallmentFeeEnabled()) {
            if ($this->getPartialInstallmentFeeInFirstInstallments()) {
                $firstInstallmentPrice = $firstInstallmentPrice + $installmentFee;
            } else if ($this->getPartialInstallmentFeeInAllInstallments()) {
                $firstInstallmentPrice = $firstInstallmentPrice + $installmentFee;
                $installmentFee = $installmentFee * ($installmentNumber - 1);
                $remainingPaymentPrice = $remainingPaymentPrice + $installmentFee;
            }
        }

        $totalPrice = $firstInstallmentPrice + $remainingPaymentPrice;

        $html = '';
        $html .= "<div class='installment-summary'>";

        $html .= "<div class='installment-first-line'>";
        $html .= "<span>" . __('Installment') . "</span>";
        $html .= "<span>" . __('Due Date') . "</span>";
        $html .= "<span>" . __('Amount') . "</span>";
        $html .= "</div>";

        $html .= "<div class='installment-line-item'>";
        $html .= "<span>1</span>";
        $html .= "<span>" . $todayDate . "</span>";
        $html .= "<span>" . $this->getFormattedPrice($currencyCode, $firstInstallmentPrice) . "</span>";

        $html .= "</div>";

        $otherInstallmentPrice = $remainingPaymentPrice / ($installmentNumber - 1);
        for ($j = 1; $j < $installmentNumber; $j++) {
            $todayDate = $this->getNextInstallmentDate($j);
            $installmentNo = $j + 1;
            $html .= "<div class='installment-line-item'>";
            $html .= "<span>$installmentNo</span>";
            $html .= "<span>" . $todayDate . "</span>";
            $html .= "<span>" . $this->getFormattedPrice($currencyCode, $otherInstallmentPrice) . "</span>";

            $html .= "</div>";
        }

        $html .= "<div class='installment-totle-item'>";
        $html .= "<span><b>" . __('TOTAL') . "</b></span>";
        $html .= "<span><b>" . $this->getFormattedPrice($currencyCode, $totalPrice) . "</b></span>";
        $html .= "</div>";

        $html .= "<div class='installment-downpayment-item'>";
        $html .= "<span><b>" . __($this->getAmtPayNowLabel()) . "</b></span>";
        $html .= "<span><b>" . $this->getFormattedPrice($currencyCode, $firstInstallmentPrice) . "</b></span>";
        $html .= "</div>";

        $html .= "<div class='installment-paidlater-item'>";
        $html .= "<span><b>" . __($this->getAmtPayLaterLabel()) . "</b></span>";
        $html .= "<span><b>" . $this->getFormattedPrice($currencyCode, $remainingPaymentPrice) . "</b></span>";
        $html .= "</div>";
        $html .= "</div>";

        return $html;
    }


    /**
     * @param $installmentNumber
     * @return string
     */
    public function getInstallmentTableCart($installmentNumber)
    {
        $firstInstallmentPrice = $otherInstallmentPrice = $installmentFee = $remainingPaymentPrice = $downPaymentPrice = 0;

        $firstInstallmentPriceAll = $otherInstallmentPriceAll = $installmentFeeAll = $remainingPaymentPriceAll = $downPaymentPriceAll = $totalPriceAll = 0;

        $currencyCode = $this->priceCurrencyInterface->getCurrency()->getCurrencyCode();
        $todayDate = date('Y-m-d');

        $quoteId = $this->cart->getQuote()->getId();
        $quote = $this->quoteFactory->create()->load($quoteId);
        $items = $quote->getAllVisibleItems();

        foreach ($items as $item) {
            if ($item->getId()) {
                $productId = $item->getProductId();
                $product = $this->productFactory->create()->load($productId);
                $mainProductPrice = (($item->getPrice() * $item->getQty()) - $item->getDiscountAmount());
                $finalPrice = $this->convertPrice($mainProductPrice);

                $configFee = $this->getConfigInstallmentFee();
                $configFeeCalculation = $this->getInstallmentFeeCalculation();

                $configDownPayment = $this->getConfigDownPayment();
                $configDownCalculation = $this->getConfigDownCalculation();

                if ($product->getDownPayment() && $product->getCalculationDownPayment() && $product->getApplyPartialPayment()) {
                    $downPayment = $product->getDownPayment();
                    $downCalculation = $product->getCalculationDownPayment();
                } else {
                    $downPayment = $configDownPayment;
                    $downCalculation = $configDownCalculation;
                }

                if ($downCalculation == 2) {
                    $firstInstallmentPrice = $finalPrice * $downPayment / 100;
                } else {
                    $firstInstallmentPrice = $downPayment;
                }

                $remainingPaymentPrice = $finalPrice - $firstInstallmentPrice;

                if ($product->getInstallmentFee() && $product->getCalInstammentFeePayment() && $product->getApplyPartialPayment()) {
                    $installmentFee = $product->getInstallmentFee();
                    $calculateInstallmentFeePayment = $product->getCalInstammentFeePayment();
                } else {
                    $installmentFee = $configFee;
                    $calculateInstallmentFeePayment = $configFeeCalculation;
                }

                if ($calculateInstallmentFeePayment == 2) {
                    $installmentFee = ($mainProductPrice * $installmentFee) / 100;
                }

                $installmentFee = $installmentFee * $item->getQty();
                $installmentFee = $this->convertPrice($installmentFee);

                if ($this->getPartialInstallmentFeeEnabled()) {
                    if ($this->getPartialInstallmentFeeInFirstInstallments()) {
                        $firstInstallmentPrice = $firstInstallmentPrice + $installmentFee;
                    } else if ($this->getPartialInstallmentFeeInAllInstallments()) {
                        $firstInstallmentPrice = $firstInstallmentPrice + $installmentFee;
                        $installmentFee = $installmentFee * ($installmentNumber - 1);
                        $remainingPaymentPrice = $remainingPaymentPrice + $installmentFee;
                    }
                }

                $totalPrice = $firstInstallmentPrice + $remainingPaymentPrice;

                $totalPriceAll += $totalPrice;
                $firstInstallmentPriceAll += $firstInstallmentPrice;
                $remainingPaymentPriceAll += $remainingPaymentPrice;
            }
        }

        $html = '';
        $html .= "<div class='installment-summary'>";

        $html .= "<div class='installment-first-line'>";
        $html .= "<span>" . __('Installment') . "</span>";
        $html .= "<span>" . __('Due Date') . "</span>";
        $html .= "<span>" . __('Amount') . "</span>";
        $html .= "</div>";

        $html .= "<div class='installment-line-item'>";
        $html .= "<span>1</span>";
        $html .= "<span>" . $todayDate . "</span>";
        $html .= "<span>" . $this->getFormattedPrice($currencyCode, $firstInstallmentPriceAll) . "</span>";

        $html .= "</div>";

        $otherInstallmentPrice = $remainingPaymentPriceAll / ($installmentNumber - 1);
        for ($j = 1; $j < $installmentNumber; $j++) {
            $todayDate = $this->getNextInstallmentDate($j);
            $installmentNo = $j + 1;
            $html .= "<div class='installment-line-item'>";
            $html .= "<span>$installmentNo</span>";
            $html .= "<span>" . $todayDate . "</span>";
            $html .= "<span>" . $this->getFormattedPrice($currencyCode, $otherInstallmentPrice) . "</span>";

            $html .= "</div>";
        }

        $html .= "<div class='installment-totle-item'>";
        $html .= "<span><b>" . __('TOTAL') . "</b></span>";
        $html .= "<span><b>" . $this->getFormattedPrice($currencyCode, $totalPriceAll) . "</b></span>";
        $html .= "</div>";

        $html .= "<div class='installment-downpayment-item'>";
        $html .= "<span><b>" . __($this->getAmtPayNowLabel()) . "</b></span>";
        $html .= "<span><b>" . $this->getFormattedPrice($currencyCode, $firstInstallmentPriceAll) . "</b></span>";
        $html .= "</div>";

        $html .= "<div class='installment-paidlater-item'>";
        $html .= "<span><b>" . __($this->getAmtPayLaterLabel()) . "</b></span>";
        $html .= "<span><b>" . $this->getFormattedPrice($currencyCode, $remainingPaymentPriceAll) . "</b></span>";
        $html .= "</div>";
        $html .= "</div>";

        return $html;
    }

    /**
     * @param int $amount
     * @return float
     */
    public function priceCurrency($amount = 0)
    {
        return $this->priceCurrencyInterface->format($amount, false, 2);
    }

    /**
     * @param int $amount
     * @param null $store
     * @param null $currency
     * @return float
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function convertPrice($amount = 0, $store = null, $currency = null)
    {
        if ($store == null) {
            $store = $this->storeManagerInterface->getStore()->getStoreId();
        }
        $rate = $this->priceCurrencyInterface->convert($amount, $store, $currency);
        return $this->priceCurrencyInterface->round($rate);
    }

    /**
     * @param $from
     * @param $to
     * @param $amount
     * @return float|int
     */
    public function convertCurrency($from, $to, $amount)
    {
        if ($from == $to) {
            return $amount;
        }
        $this->currency->load($from);
        $rate = $this->currency->getAnyRate($to);
        return $amount / $rate;
    }

    /**
     * @param $currencyCode
     * @param int $price
     * @return string
     */
    public function getFormattedPrice($currencyCode, $price = 0)
    {
        $currencySymbol = $this->currency->load($currencyCode)->getCurrencySymbol();
        $formattedPrice = $this->currency->format($price, ['symbol' => $currencySymbol, 'precision' => 2], false, false);
        return $formattedPrice;
    }

    /**
     * @return mixed
     */
    public function isLoginRequired()
    {
        return $this->scopeConfig->getValue(self::LOGIN_REQUIRED, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getCreditLimit()
    {
        return $this->scopeConfig->getValue(self::CREDIT_LIMIT, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getCreditLimitErrorMsg()
    {
        return $this->scopeConfig->getValue(self::CREDIT_LIMIT_ERROR_MSG, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return int
     */
    public function getCustomerLogin()
    {
        $customer = $this->customer->create();
        if ($customer->isLoggedIn()) {
            return 1;
        }
        return 0;
    }

    /**
     * @return int|null
     */
    public function getCustomerId()
    {
        $customer = $this->customer->create();
        if ($customer->isLoggedIn()) {
            return $customer->getId();
        }
        return 0;
    }

    /**
     * @param $orderIncrementId
     * @return int
     */
    public function getInstallmentPaidAmount($orderIncrementId)
    {
        $collection = $this->partialPaymentCollection->create();
        $collection->addFieldToFilter('order_id', $orderIncrementId);
        $paidAmount = 0;
        if ($collection->count() > 0) {
            foreach ($collection as $item) {
                $installmentCollection = $this->installmentCollection->create();
                $installmentCollection->addFieldToFilter('partial_payment_id', $item->getId());
                foreach ($installmentCollection as $installment) {
                    if ($installment->getInstallmentStatus() == 2) {
                        $paidAmount += $installment->getInstallmentAmount();
                    }
                }
                break;
            }
        }
        return $paidAmount;
    }

    /**
     * @return int
     */
    public function getLifetimePartialOrderPrice()
    {
        $total = 0;
        $customerId = $this->getCustomerId();
        $collection = $this->partialPaymentCollection->create();
        $collection->addFieldToFilter('customer_id', $customerId);
        $collection->addFieldToFilter('payment_status', ['in' => [0, 1]]);
        foreach ($collection as $item) {
            $total += $item->getOrderAmount();
        }
        return $total;
    }

    /**
     * @param $methodCode
     * @return int
     */
    public function getAllowedPaymentMethod($methodCode)
    {
        $allowedPaymentMethod = explode(',', $this->scopeConfig->getValue(self::ALLOWED_PAYMENT_METHOD, ScopeInterface::SCOPE_STORE));
        if (in_array($methodCode, $allowedPaymentMethod)) {
            return 1;
        }
        return 0;
    }

    /**
     * @param $customerGroupId
     * @return int
     */
    public function isValidCustomer($customerGroupId)
    {
        $allowedGrpId = explode(',', $this->scopeConfig->getValue(self::ALLOWED_CUSTOMER_GRP, ScopeInterface::SCOPE_STORE));
        $customGrpId = $customerGroupId;
        if (in_array($customGrpId, $allowedGrpId)) {
            return 1;
        }
        return 0;
    }

    /**
     * @return int
     */
    public function getAllowedForCustomerGrp()
    {
        $allowedGrpId = explode(',', $this->scopeConfig->getValue(self::ALLOWED_CUSTOMER_GRP, ScopeInterface::SCOPE_STORE));
        $customer = $this->customer->create();
        $customGrpId = 0;
        if ($customer->isLoggedIn()) {
            if ($customer->getCustomer()->getGroupId()) {
                $customGrpId = $customer->getCustomer()->getGroupId();
            }
        }

        if (in_array($customGrpId, $allowedGrpId)) {
            return 1;
        }
        return 0;
    }

    /**
     * @return int
     */
    public function getApplyPartialPaymentTo()
    {
        if ($this->scopeConfig->getValue(self::APPLY_PARTIAL_PAYMENT_TO, ScopeInterface::SCOPE_STORE)) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * @return int
     */
    public function getShowOnProductPage()
    {
        $applyPartialPaymentTo = $this->scopeConfig->getValue(self::APPLY_PARTIAL_PAYMENT_TO, ScopeInterface::SCOPE_STORE);
        if ($applyPartialPaymentTo) {
            return 1;
        }
        return 0;
    }

    /**
     * @return mixed
     */
    public function getApplyPartialPaymentToWhole()
    {
        return $this->scopeConfig->getValue(self::APPLY_PARTIAL_PAYMENT_TO, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return int
     */
    public function getPartialInstallmentFeeEnabled()
    {
        if ($this->scopeConfig->getValue(self::CONFIG_CUSTOM_IS_ENABLED, ScopeInterface::SCOPE_STORE) > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * @return mixed
     */
    public function getFrequency()
    {
        return $this->scopeConfig->getValue(self::PARTIAL_FREQUENCY, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getFrequencyDays()
    {
        return $this->scopeConfig->getValue(self::DAYS_COUNT, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param $methodCode
     * @param int $store
     * @return array
     */
    public function getCardsByMethods($methodCode, $store = 0)
    {
        $cards = $this->scopeConfig->getValue('payment/' . $methodCode . '/cctypes', ScopeInterface::SCOPE_STORE);
        return explode(',', $cards);
    }

    /**
     * @param $installmentNumber
     * @return false|string
     */
    public function getNextInstallmentDate($installmentNumber)
    {
        $frequency = $this->getFrequency();
        switch ($frequency) {
            case 0:
                return date("Y-m-d", strtotime("+" . $this->getFrequencyDays() * $installmentNumber . " day"));
                break;
            case 1:
                return date("Y-m-d", strtotime("+" . $installmentNumber . " week"));
                break;
            case 2:
                return date("Y-m-d", strtotime("+" . $installmentNumber . " month"));
                break;
            case 3:
                return date("Y-m-d", strtotime("+" . ($installmentNumber * 3) . " month"));
                break;
        }
    }

    /**
     * @return int
     */
    public function getPartialInstallmentFeeInAllInstallments()
    {
        if ($this->scopeConfig->getValue(self::CONFIG_CUSTOM_IS_ENABLED, ScopeInterface::SCOPE_STORE) == 2) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * @return int
     */
    public function getPartialInstallmentFeeInFirstInstallments()
    {
        if ($this->scopeConfig->getValue(self::CONFIG_CUSTOM_IS_ENABLED, ScopeInterface::SCOPE_STORE) == 1) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * @return mixed
     */
    public function getInstallmentFeeCalculation()
    {
        return $this->scopeConfig->getValue(self::CONFIG_FEE_CALCULATION, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getPartialShippingTexInclude()
    {
        return $this->scopeConfig->getValue(
            self::PARTIAL_INCLUDE_SHIPPING_TEX,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getConfigInstallmentFee()
    {
        $fee = $this->scopeConfig->getValue(self::CONFIG_CUSTOM_FEE, ScopeInterface::SCOPE_STORE);
        return $fee;
    }

    /**
     * @param null $quoteId
     * @return int
     */
    public function getPartialProductSet($quoteId = null)
    {
        $set = 0;
        if ($this->isModuleEnabled()) {
            $quote = $this->quoteFactory->create()->load($quoteId);
            $items = $quote->getAllVisibleItems();
            foreach ($items as $item) {
                if ($item->getId() && $item->getPartialApply() > 0) {
                    $set = 1;
                }
            }
        }
        return $set;
    }

    /**
     * @param $items
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function setAllMultiShipping($items)
    {
        if ($this->isModuleEnabled()) {
            foreach ($items as $item) {
                if ($item->getId()) {

                    $quoteItemId = $item->getQuoteItemId();
                    $quoteItem = $this->quoteItemFactory->create()->load($quoteItemId);

                    $isPartialApply = $quoteItem->getPartialApply();

                    if ($isPartialApply) {

                        $installmentNumber = $quoteItem->getPartialInstallmentNo();

                        $firstInstallmentPrice = $otherInstallmentPrice = $installmentFee = $remainingPaymentPrice = $downPaymentPrice = 0;
                        $productId = (int)$item->getProductId();
                        $product = $this->getProductById($productId);
                        $mainProductPrice = (($item->getPrice() * $item->getQty()) - $item->getDiscountAmount());
                        $finalPrice = $this->convertPrice($mainProductPrice);

                        $configFee = $this->getConfigInstallmentFee();
                        $configFeeCalculation = $this->getInstallmentFeeCalculation();
                        $configDownPayment = $this->getConfigDownPayment();
                        $configDownCalculation = $this->getConfigDownCalculation();

                        if ($product->getDownPayment() && $product->getCalculationDownPayment() && $product->getApplyPartialPayment()) {
                            $downPayment = $product->getDownPayment();
                            $downCalculation = $product->getCalculationDownPayment();
                        } else {
                            $downPayment = $configDownPayment;
                            $downCalculation = $configDownCalculation;
                        }

                        if ($downCalculation == 2) {
                            $firstInstallmentPrice = $finalPrice * $downPayment / 100;
                        } else {
                            $firstInstallmentPrice = $downPayment;
                        }
                        $remainingPaymentPrice = $finalPrice - $firstInstallmentPrice;

                        if ($product->getInstallmentFee() && $product->getCalInstammentFeePayment() && $product->getApplyPartialPayment()) {
                            $installmentFee = $product->getInstallmentFee();
                            $calculateInstallmentFeePayment = $product->getCalInstammentFeePayment();
                        } else {
                            $installmentFee = $configFee;
                            $calculateInstallmentFeePayment = $configFeeCalculation;
                        }

                        if ($calculateInstallmentFeePayment == 2) {
                            $installmentFee = ($mainProductPrice * $installmentFee) / 100;
                        }

                        $installmentFee = $installmentFee * $item->getQty();
                        $installmentFee = $this->convertPrice($installmentFee);

                        if ($this->getPartialInstallmentFeeEnabled()) {
                            if ($this->getPartialInstallmentFeeInFirstInstallments()) {
                                $firstInstallmentPrice = $firstInstallmentPrice + $installmentFee;
                            } else if ($this->getPartialInstallmentFeeInAllInstallments()) {
                                $firstInstallmentPrice = $firstInstallmentPrice + $installmentFee;
                                $remainingPaymentPrice = $remainingPaymentPrice + ($installmentFee * ($installmentNumber - 1));
                                $installmentFee = $installmentFee * ($installmentNumber);
                            }
                        } else {
                            $installmentFee = 0;
                        }

                        $item->setPartialInstallmentFee($installmentFee);
                        $item->setPartialInstallmentNo($installmentNumber);
                        $item->setPartialPayNow($firstInstallmentPrice);
                        $item->setPartialPayLater($remainingPaymentPrice);

                    } else {

                        $finalPrice = (($item->getPrice() * $item->getQty()) - $item->getDiscountAmount());

                        $item->setPartialInstallmentFee(0);
                        $item->setPartialInstallmentNo(0);
                        $item->setPartialPayNow($finalPrice * $item->getQty());
                        $item->setPartialPayLater(0);
                    }
                    $item->save();

                }
            }
        }
    }

    /**
     * @param null $quoteId
     * @throws \Exception
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function setAllQuoteValues($quoteId = null)
    {
        if ($this->isModuleEnabled()) {
            $quote = $this->quoteFactory->create()->load($quoteId);
            $items = $quote->getAllVisibleItems();
            foreach ($items as $item) {
                if ($item->getId()) {

                    $isPartialApply = $item->getPartialApply();

                    if ($isPartialApply) {

                        $installmentNumber = $item->getPartialInstallmentNo();

                        $firstInstallmentPrice = $otherInstallmentPrice = $installmentFee = $remainingPaymentPrice = $downPaymentPrice = 0;
                        $productId = (int)$item->getProductId();
                        $product = $this->getProductById($productId);
                        $mainProductPrice = (($item->getPrice() * $item->getQty()) - $item->getDiscountAmount());
                        $finalPrice = $this->convertPrice($mainProductPrice);

                        $configFee = $this->getConfigInstallmentFee();
                        $configFeeCalculation = $this->getInstallmentFeeCalculation();
                        $configDownPayment = $this->getConfigDownPayment();
                        $configDownCalculation = $this->getConfigDownCalculation();

                        if ($product->getDownPayment() && $product->getCalculationDownPayment() && $product->getApplyPartialPayment()) {
                            $downPayment = $product->getDownPayment();
                            $downCalculation = $product->getCalculationDownPayment();
                        } else {
                            $downPayment = $configDownPayment;
                            $downCalculation = $configDownCalculation;
                        }

                        if ($downCalculation == 2) {
                            $firstInstallmentPrice = $finalPrice * $downPayment / 100;
                        } else {
                            $firstInstallmentPrice = $downPayment;
                        }
                        $remainingPaymentPrice = $finalPrice - $firstInstallmentPrice;

                        if ($product->getInstallmentFee() && $product->getCalInstammentFeePayment() && $product->getApplyPartialPayment()) {
                            $installmentFee = $product->getInstallmentFee();
                            $calculateInstallmentFeePayment = $product->getCalInstammentFeePayment();
                        } else {
                            $installmentFee = $configFee;
                            $calculateInstallmentFeePayment = $configFeeCalculation;
                        }

                        if ($calculateInstallmentFeePayment == 2) {
                            $installmentFee = ($mainProductPrice * $installmentFee) / 100;
                        }

                        $installmentFee = $installmentFee * $item->getQty();
                        $installmentFee = $this->convertPrice($installmentFee);

                        if ($this->getPartialInstallmentFeeEnabled()) {
                            if ($this->getPartialInstallmentFeeInFirstInstallments()) {
                                $firstInstallmentPrice = $firstInstallmentPrice + $installmentFee;
                            } else if ($this->getPartialInstallmentFeeInAllInstallments()) {
                                $firstInstallmentPrice = $firstInstallmentPrice + $installmentFee;
                                $remainingPaymentPrice = $remainingPaymentPrice + ($installmentFee * ($installmentNumber - 1));
                                $installmentFee = $installmentFee * ($installmentNumber);
                            }
                        } else {
                            $installmentFee = 0;
                        }

                        $item->setPartialInstallmentFee($installmentFee);
                        $item->setPartialInstallmentNo($installmentNumber);
                        $item->setPartialPayNow($firstInstallmentPrice);
                        $item->setPartialPayLater($remainingPaymentPrice);

                    } else {

                        $finalPrice = (($item->getPrice() * $item->getQty()) - $item->getDiscountAmount());

                        $item->setPartialInstallmentFee(0);
                        $item->setPartialInstallmentNo(0);
                        $item->setPartialPayNow($finalPrice * $item->getQty());
                        $item->setPartialPayLater(0);
                    }
                    $item->save();

                }
            }
        }
    }

    /**
     * @param null $quoteId
     * @return int
     */
    public function getPartialInstallmentFee($quoteId = null)
    {
        $total = 0;
        if ($this->getPartialInstallmentFeeEnabled()) {
            $quote = $this->quoteFactory->create()->load($quoteId);
            $items = $quote->getAllVisibleItems();

            foreach ($items as $item) {
                if ($item->getId()) {
                    $total += $item->getPartialInstallmentFee();
                }
            }
        }
        return $total;
    }

    /**
     * @param $items
     * @return int
     */
    public function getPartialMultiFee($items)
    {
        $total = 0;
        if ($this->getPartialInstallmentFeeEnabled()) {
            foreach ($items as $item) {
                if ($item->getId()) {
                    $total += $item->getPartialInstallmentFee();
                }
            }
        }
        return $total;
    }

    /**
     * @param null $quoteId
     * @return int
     */
    public function calculatePartialPaynow($quoteId = null)
    {
        $total = 0;
        if ($this->isModuleEnabled()) {
            $quote = $this->quoteFactory->create()->load($quoteId);
            $items = $quote->getAllVisibleItems();

            foreach ($items as $item) {
                if ($item->getId()) {
                    $total += $item->getPartialPayNow();
                }
            }
        }
        return $total;
    }

    /**
     * @param $items
     * @return int
     */
    public function calculatePartialMultiPaynow($items)
    {
        $total = 0;
        if ($this->isModuleEnabled()) {
            foreach ($items as $item) {
                if ($item->getId()) {
                    $total += $item->getPartialPayNow();
                }
            }
        }
        return $total;
    }

    /**
     * @param null $quoteId
     * @return int
     */
    public function getMaxInstallments($quoteId = null)
    {
        $maxInstallment = 0;
        if ($this->isModuleEnabled()) {
            $quote = $this->quoteFactory->create()->load($quoteId);
            $items = $quote->getAllVisibleItems();

            foreach ($items as $item) {
                if ($item->getId()) {
                    if ($maxInstallment < $item->getPartialInstallmentNo()) {
                        $maxInstallment = $item->getPartialInstallmentNo();
                    }
                }
            }
        }
        return $maxInstallment;
    }

    /**
     * @param $items
     * @return int
     */
    public function getMaxMultiInstallments($items)
    {
        $maxInstallment = 0;
        if ($this->isModuleEnabled()) {
            foreach ($items as $item) {
                if ($item->getId()) {
                    if ($maxInstallment < $item->getPartialInstallmentNo()) {
                        $maxInstallment = $item->getPartialInstallmentNo();
                    }
                }
            }
        }
        return $maxInstallment;
    }

    /**
     * @param null $quoteId
     * @return int
     */
    public function calculatePartialPaylater($quoteId = null)
    {
        $total = 0;
        if ($this->isModuleEnabled()) {
            $quote = $this->quoteFactory->create()->load($quoteId);
            $items = $quote->getAllVisibleItems();

            foreach ($items as $item) {
                if ($item->getId()) {
                    $total += $item->getpartialPayLater();
                }
            }
        }
        return $total;
    }

    /**
     * @param $items
     * @return int
     */
    public function calculatePartialMultiPaylater($items)
    {
        $total = 0;
        if ($this->isModuleEnabled()) {
            foreach ($items as $item) {
                if ($item->getId()) {
                    $total += $item->getpartialPayLater();
                }
            }
        }
        return $total;
    }

    /**
     * @return mixed
     */
    public function getPartialInstallmentFeeLabel()
    {
        $feeLabel = $this->scopeConfig->getValue(self::CONFIG_FEE_LABEL, ScopeInterface::SCOPE_STORE);
        return $feeLabel;
    }

    /**
     * @param $productId
     * @return $this
     */
    public function getProductById($productId)
    {
        return $this->productFactory->create()->load($productId);
    }

    /**
     * @return mixed
     */
    public function getAmtPayNowLabel()
    {
        return $this->scopeConfig->getValue(self::DOWN_PAYMENT_TEX, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getAmtPayLaterLabel()
    {
        return $this->scopeConfig->getValue(self::PAYING_NOW_LATER_TEXT, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getProductpageLabel()
    {
        return $this->scopeConfig->getValue(self::PRODUCTPAGE_PAGELABEL, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getProductpageDescrition()
    {
        $html = $this->filterProvider->getPageFilter()->filter($this->scopeConfig->getValue(self::PRODUCTPAGE_DESCRIPTION, ScopeInterface::SCOPE_STORE));
        return $html;
    }

    /**
     * @return float|mixed
     */
    public function getMinimumOrderAmount()
    {
        if ($this->scopeConfig->getValue(self::APPLY_PARTIAL_PAYMENT_TO, ScopeInterface::SCOPE_STORE) == 2) {
            return $this->scopeConfig->getValue(self::WHOLE_CART_MINIMUM_AMOUNT, ScopeInterface::SCOPE_STORE);
        }
        return 0.1;
    }

    /**
     * @return mixed
     */
    public function getMinimumOrderAmountErrMsg()
    {
        return $this->scopeConfig->getValue(self::WHOLE_CART_MINIMUM_ERR_MSG, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param $purchaseRef
     * @param $currency
     * @param $amount
     * @return string
     */
    public function getPaymentForm($purchaseRef, $currency, $amount)
    {
        $sessionId = $this->sessionManager->getSessionId();
        $merchantId = $this->getMerchantId();
        $amountSend = $amount * 100;
        $description = $this->getPaymentSubject();

        $data = 'merchantid=' . $merchantId . '&amount=' . $amountSend . '&sessionid=' . $sessionId . '&purchaseref=' . $purchaseRef . '&description=' . $description . '&currency=' . $currency . '&logo=https://doya.ci/pub/media/logo/default/doya.png';

        $ch = curl_init();
        $url = $this->getInitUrl();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_PORT, 443);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/x-www-form-urlencoded"]);

        $token = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new LocalizedException(curl_errno($ch));
        }

        $html = "<form id='OrangeIvoryForm' enctype='application/x-www-form-urlencoded' name='orangeivorysubmit' action='" . $this->getGatewayUrl() . "' method='POST'>";
        $html .= "<input type='hidden' name='merchantid' value='" . $this->getMerchantId() . "' />";
        $html .= "<input type='hidden' name='amount' value='" . $amount . "' />";
        $html .= "<input type='hidden' name='token' value='" . $token . "' />";
        $html .= "<input type='hidden' name='sessionid' value='" . $sessionId . "' />";
        $html .= "<input type='hidden' name='purchaseref' value='" . $purchaseRef . "' />";
        $html .= "<input type='hidden' name='description' value='" . $this->getPaymentSubject() . "' />";
        $html .= "<input type='hidden' name='currency' value='" . $currency . "' />";
        $html .= "<input type='submit' name='ok' value='Payment' style='display:none' />";
        $html .= "</form>";

        return $html;
    }

    /**
     * @return string
     */
    public function getPaymentSubject()
    {
        $subject = trim($this->scopeConfig->getValue('general/store_information/name', ScopeInterface::SCOPE_STORE));
        if (!$subject) {
            return "Magento 2 order";
        }

        return $subject;
    }

    /**
     * @return string
     */
    public function getMerchantId()
    {
        if ($this->getMode()) {
            return $this->encryptor->decrypt($this->scopeConfig->getValue(self::CONFIG_ORANGE_SANDBOX_MERCHANT_ID, ScopeInterface::SCOPE_STORE));
        } else {
            return $this->encryptor->decrypt($this->scopeConfig->getValue(self::CONFIG_ORANGE_LIVE_MERCHANT_ID, ScopeInterface::SCOPE_STORE));
        }
    }

    /**
     * @return mixed
     */
    public function getMode()
    {
        return $this->scopeConfig->getValue(self::CONFIG_ORANGE_MODE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getInitUrl()
    {
        if ($this->getMode()) {
            return $this->scopeConfig->getValue(self::CONFIG_ORANGE_SANDBOX_INIT_URL, ScopeInterface::SCOPE_STORE);
        } else {
            return $this->scopeConfig->getValue(self::CONFIG_ORANGE_LIVE_INIT_URL, ScopeInterface::SCOPE_STORE);
        }
    }

    /**
     * @return mixed
     */
    public function getGatewayUrl()
    {
        if ($this->getMode()) {
            return $this->scopeConfig->getValue(self::CONFIG_ORANGE_SANDBOX_GATEWAY_URL, ScopeInterface::SCOPE_STORE);
        } else {
            return $this->scopeConfig->getValue(self::CONFIG_ORANGE_LIVE_GATEWAY_URL, ScopeInterface::SCOPE_STORE);
        }
    }

    /**
     * @return mixed
     */
    public function getPaymentInstructions()
    {
        return $this->scopeConfig->getValue(self::CONFIG_ORANGE_INSTRUCTIONS, ScopeInterface::SCOPE_STORE);
    }


}