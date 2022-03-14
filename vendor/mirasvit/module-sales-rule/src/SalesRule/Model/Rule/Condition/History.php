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

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Model\AbstractModel;
use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Rule\Model\Condition\Context;

class History extends AbstractCondition
{
    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * History constructor.
     * @param ResourceConnection $resource
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        ResourceConnection $resource,
        Context $context,
        array $data = []
    ) {
        $this->resource = $resource;

        parent::__construct($context, $data);
    }

    /**
     * @return $this|AbstractCondition
     */
    public function loadAttributeOptions()
    {
        $result = [
            'order_count' => __('Total Number of Orders'),
            'order_sum'   => __('Total Sales Amount'),
        ];

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
        $customerId = $model->getCustomerId();

        if (!$customerId) {
            return $this->validateAttribute(0);
        }

        $attr = $this->getData('attribute');

        $select = $this->resource->getConnection()->select()
            ->from($this->resource->getTableName('sales_order'), [
                'order_count' => new \Zend_Db_Expr('COUNT(entity_id)'),
                'order_sum'   => new \Zend_Db_Expr('SUM(base_grand_total)'),
            ])->where('customer_id=?', $customerId);

        $data = $this->resource->getConnection()->fetchRow($select);

        if (isset($data[$attr])) {
            return $this->validateAttribute($data[$attr]);
        }

        return true;
    }
}