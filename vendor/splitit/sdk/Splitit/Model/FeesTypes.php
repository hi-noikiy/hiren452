<?php
/**
 * FeesTypes
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
 * FeesTypes Class Doc Comment
 *
 * @category Class
 * @description 
 * @package  SplititSdkClient
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class FeesTypes
{
    /**
     * Possible values of this enum
     */
    const CHARGE_FEE = 'ChargeFee';
    const CHARGE_FEE_FIXED = 'ChargeFeeFixed';
    const CHARGE_FEE_VARIABLE = 'ChargeFeeVariable';
    const MONTHLY_FEE = 'MonthlyFee';
    const REVENUE_SHARE = 'RevenueShare';
    
    /**
     * Gets allowable values of the enum
     * @return string[]
     */
    public static function getAllowableEnumValues()
    {
        return [
            self::CHARGE_FEE,
            self::CHARGE_FEE_FIXED,
            self::CHARGE_FEE_VARIABLE,
            self::MONTHLY_FEE,
            self::REVENUE_SHARE,
        ];
    }
}


