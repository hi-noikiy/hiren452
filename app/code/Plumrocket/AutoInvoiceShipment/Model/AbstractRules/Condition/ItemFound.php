<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package   Plumrocket_AutoInvoiceShipment
 * @copyright Copyright (c) 2017 Plumrocket Inc. (http://www.plumrocket.com)
 * @license   http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\AutoInvoiceShipment\Model\AbstractRules\Condition;

use Magento\SalesRule\Model\Rule\Condition\Product\Found as ProductFound;

use Magento\Rule\Model\Condition\Context;

/**
 * Class ItemFound
 *
 * @method string getId()
 * @method array  getAttributeOption()
 * @method array  getAdditionalOptions()
 *
 * @method $this setType($type)
 * @method $this setAttributeOption(array $attributes)
 */
class ItemFound extends ProductFound
{
    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $connection;

    /**
     * @var Item
     */
    protected $item;

    /**
     * ItemFound constructor.
     *
     * @param Context $context
     * @param Item    $ruleConditionProduct
     * @param array   $data
     */
    public function __construct(
        Context $context,
        Item $ruleConditionProduct,
        array $data = []
    ) {
        $this->item = $ruleConditionProduct;
        parent::__construct($context, $ruleConditionProduct, $data);
        $this->setType('Plumrocket\AutoInvoiceShipment\Model\AbstractRules\Condition\ItemFound');
    }

    /**
     * Return as html
     *
     * @return string
     */
    public function asHtml()
    {
        $html = $this->getTypeElement()->getHtml() . __(
            "If an item is %1 in the order with %2 of these conditions true:",
            $this->getValueElement()->getHtml(),
            $this->getAggregatorElement()->getHtml()
        );
        if ($this->getId() != '1') {
            $html .= $this->getRemoveLinkHtml();
        }
        return $html;
    }

    /**
     * Set attributes for option list
     *
     * @return $this
     */
    public function loadAttributeOptions()
    {
        $this->setAttributeOption($this->item->getAttributeOptionsArray());
        return $this;
    }

    /**
     * Create option list
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $this->loadAttributeOptions();
        $conditions = [];

        foreach ($this->getAttributeOption() as $code => $label) {
            $conditions[] = [
                'value' => 'Plumrocket\AutoInvoiceShipment\Model\AbstractRules\Condition\Item|' . $code,
                'label' => $label
            ];
        }
        return $conditions;
    }
}
