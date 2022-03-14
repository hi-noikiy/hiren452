<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-sales-rule
 * @version   1.0.16
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SalesRule\Model\Rule\Condition;

use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\ResourceModel\Customer as CustomerResource;
use Magento\Framework\Model\AbstractModel;
use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Rule\Model\Condition\Context;

class Customer extends AbstractCondition
{
    /**
     * @var CustomerResource
     */
    private $customerResource;

    /**
     * @var CustomerFactory
     */
    private $customerFactory;

    /**
     * Customer constructor.
     * @param CustomerResource $customerResource
     * @param CustomerFactory $customerFactory
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        CustomerResource $customerResource,
        CustomerFactory $customerFactory,
        Context $context,
        array $data = []
    ) {
        $this->customerResource = $customerResource;
        $this->customerFactory  = $customerFactory;

        parent::__construct($context, $data);
    }

    /**
     * @return $this|AbstractCondition
     */
    public function loadAttributeOptions()
    {
        $attributeList = $this->customerResource->loadAllAttributes()->getAttributesByCode();

        $result = [];
        /** @var \Magento\Customer\Model\Attribute $attr */
        foreach ($attributeList as $code => $attr) {
            if (!$attr->getFrontendLabel()) {
                continue;
            }

            $result[$code] = $attr->getFrontendLabel();
        }

        asort($result);
        $this->setData('attribute_option', $result);

        return $this;
    }

    /**
     * @param AbstractModel $model
     * @return bool
     */
    public function validate(AbstractModel $model)
    {
        /** @var \Magento\Quote\Model\Quote\Address $model */

        /** @var \Magento\Customer\Model\Data\Customer $customer */
        $customer = $model->getQuote()->getCustomer();

        $data = $customer->__toArray();

        $customer = $this->customerFactory->create()->setData($data);

        return parent::validate($customer);
    }
}