<?php
/**
 * CountrySubdivisions
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

use \ArrayAccess;
use \SplititSdkClient\ObjectSerializer;

/**
 * CountrySubdivisions Class Doc Comment
 *
 * @category Class
 * @package  SplititSdkClient
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class CountrySubdivisions implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'CountrySubdivisions';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        'id' => 'int',
        'country_id' => 'int',
        'iso_code' => 'string',
        'description' => 'string',
        'iso2_code' => 'string',
        'utc_time_offset_in_mins' => 'int',
        'country' => '\SplititSdkClient\Model\Countries',
        'state_limit_rule_datas' => '\SplititSdkClient\Model\StateLimitRuleDatas[]',
        'zip_address_details' => '\SplititSdkClient\Model\ZipAddressDetails[]'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerFormats = [
        'id' => 'int64',
        'country_id' => 'int64',
        'iso_code' => null,
        'description' => null,
        'iso2_code' => null,
        'utc_time_offset_in_mins' => 'int32',
        'country' => null,
        'state_limit_rule_datas' => null,
        'zip_address_details' => null
    ];

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function swaggerTypes()
    {
        return self::$swaggerTypes;
    }

    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function swaggerFormats()
    {
        return self::$swaggerFormats;
    }

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @var string[]
     */
    protected static $attributeMap = [
        'id' => 'Id',
        'country_id' => 'CountryId',
        'iso_code' => 'IsoCode',
        'description' => 'Description',
        'iso2_code' => 'Iso2Code',
        'utc_time_offset_in_mins' => 'UtcTimeOffsetInMins',
        'country' => 'Country',
        'state_limit_rule_datas' => 'StateLimitRuleDatas',
        'zip_address_details' => 'ZipAddressDetails'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'id' => 'setId',
        'country_id' => 'setCountryId',
        'iso_code' => 'setIsoCode',
        'description' => 'setDescription',
        'iso2_code' => 'setIso2Code',
        'utc_time_offset_in_mins' => 'setUtcTimeOffsetInMins',
        'country' => 'setCountry',
        'state_limit_rule_datas' => 'setStateLimitRuleDatas',
        'zip_address_details' => 'setZipAddressDetails'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'id' => 'getId',
        'country_id' => 'getCountryId',
        'iso_code' => 'getIsoCode',
        'description' => 'getDescription',
        'iso2_code' => 'getIso2Code',
        'utc_time_offset_in_mins' => 'getUtcTimeOffsetInMins',
        'country' => 'getCountry',
        'state_limit_rule_datas' => 'getStateLimitRuleDatas',
        'zip_address_details' => 'getZipAddressDetails'
    ];

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @return array
     */
    public static function attributeMap()
    {
        return self::$attributeMap;
    }

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @return array
     */
    public static function setters()
    {
        return self::$setters;
    }

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @return array
     */
    public static function getters()
    {
        return self::$getters;
    }

    /**
     * The original name of the model.
     *
     * @return string
     */
    public function getModelName()
    {
        return self::$swaggerModelName;
    }

    

    

    /**
     * Associative array for storing property values
     *
     * @var mixed[]
     */
    protected $container = [];

    /**
     * Constructor
     *
     * @param mixed[] $data Associated array of property values
     *                      initializing the model
     */
    public function __construct(array $data = null)
    {
        $this->container['id'] = isset($data['id']) ? $data['id'] : null;
        $this->container['country_id'] = isset($data['country_id']) ? $data['country_id'] : null;
        $this->container['iso_code'] = isset($data['iso_code']) ? $data['iso_code'] : null;
        $this->container['description'] = isset($data['description']) ? $data['description'] : null;
        $this->container['iso2_code'] = isset($data['iso2_code']) ? $data['iso2_code'] : null;
        $this->container['utc_time_offset_in_mins'] = isset($data['utc_time_offset_in_mins']) ? $data['utc_time_offset_in_mins'] : null;
        $this->container['country'] = isset($data['country']) ? $data['country'] : null;
        $this->container['state_limit_rule_datas'] = isset($data['state_limit_rule_datas']) ? $data['state_limit_rule_datas'] : null;
        $this->container['zip_address_details'] = isset($data['zip_address_details']) ? $data['zip_address_details'] : null;
    }

    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalidProperties = [];

        if ($this->container['id'] === null) {
            $invalidProperties[] = "'id' can't be null";
        }
        if ($this->container['country_id'] === null) {
            $invalidProperties[] = "'country_id' can't be null";
        }
        if ($this->container['utc_time_offset_in_mins'] === null) {
            $invalidProperties[] = "'utc_time_offset_in_mins' can't be null";
        }
        return $invalidProperties;
    }

    /**
     * Validate all the properties in the model
     * return true if all passed
     *
     * @return bool True if all properties are valid
     */
    public function valid()
    {
        return count($this->listInvalidProperties()) === 0;
    }


    /**
     * Gets id
     *
     * @return int
     */
    public function getId()
    {
        return $this->container['id'];
    }

    /**
     * Sets id
     *
     * @param int $id id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->container['id'] = $id;

        return $this;
    }

    /**
     * Gets country_id
     *
     * @return int
     */
    public function getCountryId()
    {
        return $this->container['country_id'];
    }

    /**
     * Sets country_id
     *
     * @param int $country_id country_id
     *
     * @return $this
     */
    public function setCountryId($country_id)
    {
        $this->container['country_id'] = $country_id;

        return $this;
    }

    /**
     * Gets iso_code
     *
     * @return string
     */
    public function getIsoCode()
    {
        return $this->container['iso_code'];
    }

    /**
     * Sets iso_code
     *
     * @param string $iso_code iso_code
     *
     * @return $this
     */
    public function setIsoCode($iso_code)
    {
        $this->container['iso_code'] = $iso_code;

        return $this;
    }

    /**
     * Gets description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->container['description'];
    }

    /**
     * Sets description
     *
     * @param string $description description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        $this->container['description'] = $description;

        return $this;
    }

    /**
     * Gets iso2_code
     *
     * @return string
     */
    public function getIso2Code()
    {
        return $this->container['iso2_code'];
    }

    /**
     * Sets iso2_code
     *
     * @param string $iso2_code iso2_code
     *
     * @return $this
     */
    public function setIso2Code($iso2_code)
    {
        $this->container['iso2_code'] = $iso2_code;

        return $this;
    }

    /**
     * Gets utc_time_offset_in_mins
     *
     * @return int
     */
    public function getUtcTimeOffsetInMins()
    {
        return $this->container['utc_time_offset_in_mins'];
    }

    /**
     * Sets utc_time_offset_in_mins
     *
     * @param int $utc_time_offset_in_mins utc_time_offset_in_mins
     *
     * @return $this
     */
    public function setUtcTimeOffsetInMins($utc_time_offset_in_mins)
    {
        $this->container['utc_time_offset_in_mins'] = $utc_time_offset_in_mins;

        return $this;
    }

    /**
     * Gets country
     *
     * @return \SplititSdkClient\Model\Countries
     */
    public function getCountry()
    {
        return $this->container['country'];
    }

    /**
     * Sets country
     *
     * @param \SplititSdkClient\Model\Countries $country country
     *
     * @return $this
     */
    public function setCountry($country)
    {
        $this->container['country'] = $country;

        return $this;
    }

    /**
     * Gets state_limit_rule_datas
     *
     * @return \SplititSdkClient\Model\StateLimitRuleDatas[]
     */
    public function getStateLimitRuleDatas()
    {
        return $this->container['state_limit_rule_datas'];
    }

    /**
     * Sets state_limit_rule_datas
     *
     * @param \SplititSdkClient\Model\StateLimitRuleDatas[] $state_limit_rule_datas state_limit_rule_datas
     *
     * @return $this
     */
    public function setStateLimitRuleDatas($state_limit_rule_datas)
    {
        $this->container['state_limit_rule_datas'] = $state_limit_rule_datas;

        return $this;
    }

    /**
     * Gets zip_address_details
     *
     * @return \SplititSdkClient\Model\ZipAddressDetails[]
     */
    public function getZipAddressDetails()
    {
        return $this->container['zip_address_details'];
    }

    /**
     * Sets zip_address_details
     *
     * @param \SplititSdkClient\Model\ZipAddressDetails[] $zip_address_details zip_address_details
     *
     * @return $this
     */
    public function setZipAddressDetails($zip_address_details)
    {
        $this->container['zip_address_details'] = $zip_address_details;

        return $this;
    }
    /**
     * Returns true if offset exists. False otherwise.
     *
     * @param integer $offset Offset
     *
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    /**
     * Gets offset.
     *
     * @param integer $offset Offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    /**
     * Sets value based on offset.
     *
     * @param integer $offset Offset
     * @param mixed   $value  Value to be set
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /**
     * Unsets offset.
     *
     * @param integer $offset Offset
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    /**
     * Gets the string presentation of the object
     *
     * @return string
     */
    public function __toString()
    {
        if (defined('JSON_PRETTY_PRINT')) { // use JSON pretty print
            return json_encode(
                ObjectSerializer::sanitizeForSerialization($this),
                JSON_PRETTY_PRINT
            );
        }

        return json_encode(ObjectSerializer::sanitizeForSerialization($this));
    }
}

