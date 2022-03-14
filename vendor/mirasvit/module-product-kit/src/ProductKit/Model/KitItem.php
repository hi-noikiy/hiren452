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
 * @package   mirasvit/module-product-kit
 * @version   1.0.29
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\ProductKit\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Mirasvit\Core\Service\CompatibilityService;
use Mirasvit\ProductKit\Api\Data\KitItemInterface;
use Mirasvit\ProductKit\Model\Rule\Rule;
use Mirasvit\ProductKit\Model\Rule\RuleFactory;
use Mirasvit\ProductKit\Service\Serializer;

class KitItem extends AbstractModel implements KitItemInterface
{
    /**
     * @var Rule
     */
    private $rule;

    private $ruleFactory;

    private $serializer;

    public function __construct(
        RuleFactory $ruleFactory,
        Serializer $serializer,
        Context $context,
        Registry $registry
    ) {
        $this->ruleFactory = $ruleFactory;
        $this->serializer  = $serializer;

        parent::__construct($context, $registry);
    }

    protected function _construct()
    {
        $this->_init(ResourceModel\KitItem::class);
    }

    public function getId()
    {
        return (int)$this->getData(self::ID);
    }

    public function setId($value)
    {
        return $this->setData(self::ID, $value);
    }

    public function getKitId()
    {
        return (int)$this->getData(self::KIT_ID);
    }

    public function setKitId($value)
    {
        return $this->setData(self::KIT_ID, $value);
    }

    public function getProductId()
    {
        return (int)$this->getData(self::PRODUCT_ID);
    }

    public function setProductId($value)
    {
        return $this->setData(self::PRODUCT_ID, $value);
    }

    public function getPosition()
    {
        return (int)$this->getData(self::POSITION);
    }

    public function setPosition($value)
    {
        return $this->setData(self::POSITION, $value);
    }

    public function isOptional()
    {
        return (bool)$this->getData(self::IS_OPTIONAL);
    }

    public function setIsOptional($value)
    {
        return $this->setData(self::IS_OPTIONAL, (bool)$value);
    }

    public function isPrimary()
    {
        return (bool)$this->getData(self::IS_PRIMARY);
    }

    public function setIsPrimary($value)
    {
        return $this->setData(self::IS_PRIMARY, (bool)$value);
    }

    public function getQty()
    {
        return (int)$this->getData(self::QTY);
    }

    public function setQty($value)
    {
        return $this->setData(self::QTY, $value);
    }

    public function getDiscountType()
    {
        return $this->getData(self::DISCOUNT_TYPE);
    }

    public function setDiscountType($value)
    {
        return $this->setData(self::DISCOUNT_TYPE, $value);
    }

    public function getDiscountAmount()
    {
        return $this->getData(self::DISCOUNT_AMOUNT);
    }

    public function setDiscountAmount($value)
    {
        return $this->setData(self::DISCOUNT_AMOUNT, $value);
    }

    public function getConditions()
    {
        $conditions = $this->getData(self::CONDITIONS);

        if (CompatibilityService::is21() || CompatibilityService::is20()) {
            $conditions = $this->serializer->unserialize($conditions);
            $conditions = $this->serializer->getSerialiser()->serialize($conditions);
        }

        return $conditions;
    }

    public function setConditions($value)
    {
        return $this->setData(self::CONDITIONS, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function getRule()
    {
        if (empty($this->rule)) {
            $this->rule = $this->ruleFactory->create();
            $conditions = $this->getConditions();
            if ($conditions) {
                $this->rule->setConditionsSerialized($conditions);
            }
        }

        return $this->rule;
    }
}
