<?php

namespace Meetanshi\Inquiry\Model;

use Meetanshi\Inquiry\Api\Data\GridInterface;
use Magento\Framework\Model\AbstractModel;

class Inquiry extends AbstractModel implements GridInterface
{
    const CACHE_TAG = 'dealer_inquiry';
    protected $cacheTag = 'dealer_inquiry';
    protected $eventPrefix = 'dealer_inquiry';

    protected function _construct()
    {
        $this->_init('Meetanshi\Inquiry\Model\ResourceModel\Inquiry');
    }

    public function getDealerId()
    {
        return $this->getData(self::DEALER_ID);
    }

    public function setDealerId($dealerId)
    {
        return $this->setData(self::DEALER_ID, $dealerId);
    }

    public function getFirstName()
    {
        return $this->getData(self::FIRST_NAME);
    }

    public function setFirstName($firstName)
    {
        return $this->setData(self::FIRST_NAME, $firstName);
    }

    public function getLastName()
    {
        return $this->getData(self::LAST_NAME);
    }

    public function setLastName($lastName)
    {
        return $this->setData(self::LAST_NAME, $lastName);
    }

    public function getEmail()
    {
        return $this->getData(self::EMAIL);
    }

    public function setEmail($email)
    {
        return $this->setData(self::EMAIL, $email);
    }

    public function getStoreView()
    {
        return $this->getData(self::STORE_VIEW);
    }

    public function setStoreView($storeView)
    {
        return $this->setData(self::STORE_VIEW, $storeView);
    }

    public function getIsCustomerCreated()
    {
        return $this->getData(self::IS_CUSTOMER_CREATED);
    }

    public function setIsCustomerCreated($isCustomerCreated)
    {
        return $this->setData(self::IS_CUSTOMER_CREATED, $isCustomerCreated);
    }

    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }
}
