<?php

namespace Meetanshi\Partialpro\Block\Adminhtml\Manage\Method;

use Magento\Framework\View\Element\Template\Context;
use Magento\Payment\Helper\Data;
use Magento\Payment\Model\Checks\SpecificationFactory;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Quote\Model\QuoteFactory;
use Meetanshi\Partialpro\Block\Adminhtml\Manage\Edit;
use Magento\Payment\Block\Form\Container;
use Meetanshi\Partialpro\Helper\Data as partialData;

class Form extends Container
{
    protected $_sessionQuote;
    protected $quoteFactory;
    protected $pratialView;
    protected $partialHelper;

    public function __construct(
        Context $context,
        Data $paymentHelper,
        SpecificationFactory $methodSpecificationFactory,
        CustomerFactory $customerFactory,
        CustomerRepositoryInterface $customerRepository,
        Product $product,
        QuoteFactory $quote,
        Edit $partialView,
        partialData $partialData,
        array $data = []
    )
    {

        $this->_logger = $context->getLogger();
        $this->storeManager = $context->getStoreManager();
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
        $this->_product = $product;
        $this->quoteFactory = $quote;
        $this->pratialView = $partialView;
        $this->partialHelper = $partialData;
        parent::__construct($context, $paymentHelper, $methodSpecificationFactory, $data);
    }

    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('Meetanshi_Partialpro::summary/view/form.phtml');
    }

    public function getQuote()
    {
        return $this->pratialView->tempQuote();
    }

    public function getPayAllInstallments()
    {
        return $this->partialHelper->getPayAllInstallments();
    }

    protected function _canUseMethod($method)
    {
        return $method && ($method->isOffline()) && parent::_canUseMethod($method);
    }

    public function hasMethods()
    {
        $methods = $this->getMethods();
        if (is_array($methods) && sizeof($methods)) {
            return true;
        }
        return false;
    }

    public function getSelectedMethodCode()
    {
        $methods = $this->getMethods();
        if (sizeof($methods) == 1) {
            foreach ($methods as $method) {
                return $method->getCode();
            }
        }

        $currentMethodCode = $this->getQuote()->getPayment()->getMethod();
        if ($currentMethodCode) {
            return $currentMethodCode;
        }

        return false;
    }

    public function hasSsCardType()
    {
        $availableTypes = explode(',', $this->getQuote()->getPayment()->getMethod()->getConfigData('cctypes'));
        $ssPresenations = array_intersect(['SS', 'SM', 'SO'], $availableTypes);
        if ($availableTypes && sizeof($ssPresenations) > 0) {
            return true;
        }
        return false;
    }

    public function setMethodFormTemplate($method = '', $template = '')
    {
        if (!empty($method) && !empty($template)) {
            if ($block = $this->getChildBlock('payment.method.' . $method)) {
                $block->setTemplate($template);
            }
        }
        return $this;
    }
}
