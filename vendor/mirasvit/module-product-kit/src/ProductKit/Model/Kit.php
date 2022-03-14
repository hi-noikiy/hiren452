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
use Mirasvit\ProductKit\Api\Data\KitInterface;

class Kit extends AbstractModel implements KitInterface
{
    protected function _construct()
    {
        $this->_init(ResourceModel\Kit::class);
    }

    public function getId()
    {
        return $this->getData(self::ID);
    }

    public function getName()
    {
        return $this->getData(self::NAME);
    }

    public function setName($value)
    {
        return $this->setData(self::NAME, $value);
    }

    public function getTitle()
    {
        return $this->getData(self::BLOCK_TITLE);
    }

    public function setTitle($value)
    {
        return $this->setData(self::BLOCK_TITLE, $value);
    }

    public function getLabel()
    {
        return $this->getData(self::LABEL);
    }

    public function setLabel($value)
    {
        return $this->setData(self::LABEL, $value);
    }

    public function isActive()
    {
        return $this->getData(self::IS_ACTIVE);
    }

    public function setIsActive($value)
    {
        return $this->setData(self::IS_ACTIVE, $value);
    }

    public function isSmart()
    {
        return (bool)$this->getData(self::IS_SMART);
    }

    public function setIsSmart($value)
    {
        return $this->setData(self::IS_SMART, $value);
    }

    public function getStoreIds()
    {
        return $this->getListData(self::STORE_IDS);
    }

    public function setStoreIds(array $value)
    {
        return $this->setListData(self::STORE_IDS, $value);
    }

    public function getCustomerGroupIds()
    {
        return $this->getListData(self::CUSTOMER_GROUP_IDS);
    }

    public function setCustomerGroupIds(array $value)
    {
        return $this->setListData(self::CUSTOMER_GROUP_IDS, $value);
    }

    public function getPriority()
    {
        return $this->getData(self::PRIORITY);
    }

    public function setPriority($value)
    {
        return $this->setData(self::PRIORITY, $value);
    }

    public function isStopRulesProcessing()
    {
        return $this->getData(self::STOP_RULES_PROCESSING);
    }

    public function setStopRulesProcessing($value)
    {
        return $this->setData(self::STOP_RULES_PROCESSING, $value);
    }

    public function getPricePattern()
    {
        return $this->getData(self::PRICE_PATTERN);
    }

    public function setPricePattern($value)
    {
        return $this->setData(self::PRICE_PATTERN, $value);
    }

    /**
     * @param string $key
     * @return array
     */
    public function getListData($key)
    {
        return explode(',', $this->getData($key));
    }

    /**
     * @param string       $key
     * @param string|array $value
     * @return Kit
     */
    private function setListData($key, $value)
    {
        if (!is_array($value)) {
            $value = explode(',', $value);
        }

        return $this->setData($key, implode(',', $value));
    }
}
