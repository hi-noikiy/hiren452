<?php

namespace Meetanshi\Inquiry\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Customer\Model\Customer as Custom;
use Magento\Framework\Escaper;
use Meetanshi\Inquiry\Helper\Data;
use Magento\Store\Model\StoreManagerInterface;

class Customer extends Column
{
    protected $systemStore;
    protected $storeManager;
    protected $helper;
    protected $customer;
    protected $escaper;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Custom $customer,
        Escaper $escaper,
        Data $helper,
        StoreManagerInterface $storeManager,
        array $components = [],
        array $data = []
    )
    {
        $this->customer = $customer;
        $this->escaper = $escaper;
        $this->helper = $helper;
        $this->storeManager = $storeManager;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if ($this->customerExists($item['email'], $item['is_customer_created'])) :
                    $customer = "<span style='display: block; background: #d0e5a9 none repeat scroll 0 0; border: 1px solid #5b8116; color: #185b00; font-weight: 700; line-height: 17px; padding: 0 3px; text-align: center; text-transform: uppercase'>Created</span>";
                    $item[$this->getData('name')] = $customer;
                else :
                    $customer = "<span style='display: block; background: #f9d4d4 none repeat scroll 0 0; border: 1px solid #e22626; color: #e22626; font-weight: 700; line-height: 17px; padding: 0 3px; text-align: center; text-transform: uppercase'>Not Created</span>";
                    $item[$this->getData('name')] = $customer;
                endif;
            }
        }
        return $dataSource;
    }

    public function customerExists($email, $websiteId = null)
    {
        $this->helper->printLog($this->storeManager->getStore()->getWebsiteId());
        $baseCustomer = $this->customer;
        $baseCustomer->setWebsiteId($this->storeManager->getStore()->getWebsiteId());
        $baseCustomer->loadByEmail($email);
        $customer = $this->customer;
        if ($websiteId) {
            $customer->setWebsiteId($websiteId);
        } elseif ($baseCustomer->getWebsiteId()) {
            $customer->setWebsiteId($baseCustomer->getWebsiteId());
        } else {
            return false;
        }
        $customer->loadByEmail($email);
        if ($customer->getId()) {
            return true;
        }
        return false;
    }
}
