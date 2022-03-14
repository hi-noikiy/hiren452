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
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Newsletterpopup\Model\Backend\FieldsTag;

/**
 * Class InfusionSoft
 */
class InfusionSoft extends \Plumrocket\Newsletterpopup\Model\Backend\AbstractFieldsTag
{
    /**
     * @return array
     */
    public function getFields()
    {
        return [
            'email' => 'Email',
            'firstname' => 'FirstName',
            'middlename' => 'MiddleName',
            'lastname' => 'LastName',
            'suffix' => 'Suffix',
            'dob' => 'Birthday',
            'gender' => '',
            'taxvat' => '',
            'prefix' => '',
            'telephone' => 'Phone1',
            'fax' => 'Fax1',
            'company' => 'Company',
            'street' => 'StreetAddress1',
            'city' => 'City',
            'country_id' => 'Country',
            'region' => 'State',
            'postcode' => 'PostalCode',
            'coupon' => '',
        ];
    }
}
