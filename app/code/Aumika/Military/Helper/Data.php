<?php
namespace Aumika\Military\Helper;


use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Data extends AbstractHelper
{
    
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Group $customerGroup
    ) {
       
       $this->_customerGroup = $customerGroup;
       parent::__construct($context);
    }
    public function getCustomerGroup($id)
    {
        $customerGroupName = $this->_customerGroup->load($id);
        return $customerGroupName->getCustomerGroupCode();
    }
}
