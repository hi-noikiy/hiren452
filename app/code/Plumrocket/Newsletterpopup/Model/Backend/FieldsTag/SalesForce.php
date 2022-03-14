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
 * @package     Plumrocket_Newsletterpopup
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Newsletterpopup\Model\Backend\FieldsTag;

/**
 * Class SalesForce
 */
class SalesForce extends \Plumrocket\Newsletterpopup\Model\Backend\AbstractFieldsTag
{
    /**
     * @return array
     */
    public function getFields()
    {
        return [
            'email' => 'email',
            'firstname' => 'FirstName',
            'middlename' => 'MiddleName',
            'lastname' => 'LastName',
            'suffix' => '',
            'dob' => 'Birthdate',
            'gender' => 'gender',
            'taxvat' => '',
            'prefix' => '',
            'telephone' => 'Phone',
            'fax' => 'fax',
            'company' => '',
            'street' => 'MailingStreet',
            'city' => 'MailingCity',
            'country_id' => 'MailingCountry',
            'region' => 'MailingState',
            'postcode' => 'MailingPostalCode',
            'coupon' => '',
        ];
    }
}