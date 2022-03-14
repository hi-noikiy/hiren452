<?php

namespace Meetanshi\Inquiry\Api\Data;

interface GridInterface
{
    const DEALER_ID = 'dealer_id';
    const FIRST_NAME = 'first_name';
    const LAST_NAME = 'last_name';
    const EMAIL = 'email';
    const STORE_VIEW = 'store_view';
    const IS_CUSTOMER_CREATED = "is_customer_created";
    const CREATED_AT = 'created_at';

    public function getDealerId();

    public function setDealerId($dealerId);

    public function getFirstName();

    public function setFirstName($firstName);

    public function getLastName();

    public function setLastName($lastName);

    public function getEmail();

    public function setEmail($email);

    public function getStoreView();

    public function setStoreView($storeView);

    public function getIsCustomerCreated();

    public function setIsCustomerCreated($isCustomerCreated);

    public function getCreatedAt();

    public function setCreatedAt($createdAt);
}
