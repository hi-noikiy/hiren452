<?php

namespace Aumika\Military\Block;

class Military extends \Magento\Framework\View\Element\Template
{

    /**
     * View constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Api\AccountManagementInterface $accountManagement,
        \Magento\Directory\Model\RegionFactory $regionColFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Group $customerGroup,
        array $data = []
    ) {
        $this->accountManagement = $accountManagement;
        $this->_coreRegistry = $registry;
        $this->regionColFactory = $regionColFactory;
        $this->customerFactory = $customerFactory;
        $this->_customerSession = $customerSession;
        $this->_customerGroup = $customerGroup;
        parent::__construct($context, $data);
    }

    /**
     * Get form action URL for POST booking request
     *
     * @return string
     */
    public function getFormAction()
    {
        return $this->getUrl('military/index/militaryformsave', ['_secure' => true]);
    }
    public function getMilitaryFormAction()
    {
        return $this->getUrl('military/index/afterloginmilitary', ['_secure' => true]);
    }
    public function getCustomerGroup($id)
    {
        //$customerGroupId = $this->_customerSession->getCustomer()->getId();
        //echo '<pre>'; var_dump($customerGroupId); die('sajdfhgsja');
        $customerGroupName = $this->_customerGroup->load($id);
        return $customerGroupName->getCustomerGroupCode();
    }
    public function getCustomer()
    {
        return $this->_customerSession->getCustomer();
    }
}
