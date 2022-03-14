<?php
/**
 * MerchantVertical
 *
 * PHP version 5
 *
 * @category Class
 * @package  SplititSdkClient
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */

/**
 * splitit-web-api-public-sdk
 *
 * No description provided (generated by Swagger Codegen https://github.com/swagger-api/swagger-codegen)
 *
 * OpenAPI spec version: 1.0.0
 * 
 * Generated by: https://github.com/swagger-api/swagger-codegen.git
 * Swagger Codegen version: 2.4.12
 */

/**
 * NOTE: This class is auto generated by the swagger code generator program.
 * https://github.com/swagger-api/swagger-codegen
 * Do not edit the class manually.
 */

namespace SplititSdkClient\Model;
use \SplititSdkClient\ObjectSerializer;

/**
 * MerchantVertical Class Doc Comment
 *
 * @category Class
 * @description 
 * @package  SplititSdkClient
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class MerchantVertical
{
    /**
     * Possible values of this enum
     */
    const NOT_SET = 'NotSet';
    const COMMUNICATION = 'Communication';
    const HEALTH_BEAUTY = 'HealthBeauty';
    const JEWELLERY = 'Jewellery';
    const AUTO = 'Auto';
    const OFFICE_SUPPLIES = 'OfficeSupplies';
    const ELECTRONICS = 'Electronics';
    const TRAVEL = 'Travel';
    const APPAREL_ACCESSORIES = 'ApparelAccessories';
    const HARDWARE_HOME_IMPROVEMENT = 'HardwareHomeImprovement';
    const SPECIALITY = 'Speciality';
    const MEDICAL_HEALTH = 'MedicalHealth';
    const SPORTING_GOODS = 'SportingGoods';
    const HOUSEWARE_HOMEFURNISHINGS = 'Houseware_Homefurnishings';
    
    /**
     * Gets allowable values of the enum
     * @return string[]
     */
    public static function getAllowableEnumValues()
    {
        return [
            self::NOT_SET,
            self::COMMUNICATION,
            self::HEALTH_BEAUTY,
            self::JEWELLERY,
            self::AUTO,
            self::OFFICE_SUPPLIES,
            self::ELECTRONICS,
            self::TRAVEL,
            self::APPAREL_ACCESSORIES,
            self::HARDWARE_HOME_IMPROVEMENT,
            self::SPECIALITY,
            self::MEDICAL_HEALTH,
            self::SPORTING_GOODS,
            self::HOUSEWARE_HOMEFURNISHINGS,
        ];
    }
}


