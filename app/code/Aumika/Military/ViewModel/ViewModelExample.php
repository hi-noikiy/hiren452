<?php

namespace Aumika\Military\ViewModel;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Http\Context;
use Magento\Customer\Model\SessionFactory;
/**
 * Class ViewModelExample
 * @package MagentoCoders\MyAllData\ViewModel
 */
class ViewModelExample implements ArgumentInterface
{
    /** @var ScopeConfigInterface */
    protected $customerSession;
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var Context
     */
    private $httpContext;

    /**
     * ViewModelExample constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Context $httpContext,
        \Magento\Customer\Model\Group $customerGroup,
        SessionFactory $customerSession
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->httpContext = $httpContext;
        $this->customerSession = $customerSession;
        $this->_customerGroup = $customerGroup;
    }
    /**
     * @return string
     */
    public function getCustomerGroup()
    {
        $groupId = $this->customerSession->create()->getCustomer()->getGroupId();
        $customerGroupName = $this->_customerGroup->load($groupId);
        return $customerGroupName->getCustomerGroupCode();
    }
    
}