<?php
/**
 * TouchPoints
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
 * TouchPoints Class Doc Comment
 *
 * @category Class
 * @package  SplititSdkClient
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class TouchPoints implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'TouchPoints';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        'id' => 'int',
        'name' => 'string',
        'code' => 'string',
        'default_color_values_id' => 'int',
        'logo_supported' => 'bool',
        'owner' => 'string',
        'url' => 'string',
        'business_party' => '\SplititSdkClient\Model\BusinessParty',
        'config_keys' => '\SplititSdkClient\Model\ConfigKeys[]',
        'versioned_touch_points' => '\SplititSdkClient\Model\VersionedTouchPoints[]'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerFormats = [
        'id' => 'int64',
        'name' => null,
        'code' => null,
        'default_color_values_id' => 'int64',
        'logo_supported' => null,
        'owner' => null,
        'url' => null,
        'business_party' => null,
        'config_keys' => null,
        'versioned_touch_points' => null
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
        'name' => 'Name',
        'code' => 'Code',
        'default_color_values_id' => 'DefaultColorValuesId',
        'logo_supported' => 'LogoSupported',
        'owner' => 'Owner',
        'url' => 'Url',
        'business_party' => 'BusinessParty',
        'config_keys' => 'ConfigKeys',
        'versioned_touch_points' => 'VersionedTouchPoints'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'id' => 'setId',
        'name' => 'setName',
        'code' => 'setCode',
        'default_color_values_id' => 'setDefaultColorValuesId',
        'logo_supported' => 'setLogoSupported',
        'owner' => 'setOwner',
        'url' => 'setUrl',
        'business_party' => 'setBusinessParty',
        'config_keys' => 'setConfigKeys',
        'versioned_touch_points' => 'setVersionedTouchPoints'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'id' => 'getId',
        'name' => 'getName',
        'code' => 'getCode',
        'default_color_values_id' => 'getDefaultColorValuesId',
        'logo_supported' => 'getLogoSupported',
        'owner' => 'getOwner',
        'url' => 'getUrl',
        'business_party' => 'getBusinessParty',
        'config_keys' => 'getConfigKeys',
        'versioned_touch_points' => 'getVersionedTouchPoints'
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
        $this->container['name'] = isset($data['name']) ? $data['name'] : null;
        $this->container['code'] = isset($data['code']) ? $data['code'] : null;
        $this->container['default_color_values_id'] = isset($data['default_color_values_id']) ? $data['default_color_values_id'] : null;
        $this->container['logo_supported'] = isset($data['logo_supported']) ? $data['logo_supported'] : null;
        $this->container['owner'] = isset($data['owner']) ? $data['owner'] : null;
        $this->container['url'] = isset($data['url']) ? $data['url'] : null;
        $this->container['business_party'] = isset($data['business_party']) ? $data['business_party'] : null;
        $this->container['config_keys'] = isset($data['config_keys']) ? $data['config_keys'] : null;
        $this->container['versioned_touch_points'] = isset($data['versioned_touch_points']) ? $data['versioned_touch_points'] : null;
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
        if ($this->container['default_color_values_id'] === null) {
            $invalidProperties[] = "'default_color_values_id' can't be null";
        }
        if ($this->container['logo_supported'] === null) {
            $invalidProperties[] = "'logo_supported' can't be null";
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
     * Gets name
     *
     * @return string
     */
    public function getName()
    {
        return $this->container['name'];
    }

    /**
     * Sets name
     *
     * @param string $name name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->container['name'] = $name;

        return $this;
    }

    /**
     * Gets code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->container['code'];
    }

    /**
     * Sets code
     *
     * @param string $code code
     *
     * @return $this
     */
    public function setCode($code)
    {
        $this->container['code'] = $code;

        return $this;
    }

    /**
     * Gets default_color_values_id
     *
     * @return int
     */
    public function getDefaultColorValuesId()
    {
        return $this->container['default_color_values_id'];
    }

    /**
     * Sets default_color_values_id
     *
     * @param int $default_color_values_id default_color_values_id
     *
     * @return $this
     */
    public function setDefaultColorValuesId($default_color_values_id)
    {
        $this->container['default_color_values_id'] = $default_color_values_id;

        return $this;
    }

    /**
     * Gets logo_supported
     *
     * @return bool
     */
    public function getLogoSupported()
    {
        return $this->container['logo_supported'];
    }

    /**
     * Sets logo_supported
     *
     * @param bool $logo_supported logo_supported
     *
     * @return $this
     */
    public function setLogoSupported($logo_supported)
    {
        $this->container['logo_supported'] = $logo_supported;

        return $this;
    }

    /**
     * Gets owner
     *
     * @return string
     */
    public function getOwner()
    {
        return $this->container['owner'];
    }

    /**
     * Sets owner
     *
     * @param string $owner owner
     *
     * @return $this
     */
    public function setOwner($owner)
    {
        $this->container['owner'] = $owner;

        return $this;
    }

    /**
     * Gets url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->container['url'];
    }

    /**
     * Sets url
     *
     * @param string $url url
     *
     * @return $this
     */
    public function setUrl($url)
    {
        $this->container['url'] = $url;

        return $this;
    }

    /**
     * Gets business_party
     *
     * @return \SplititSdkClient\Model\BusinessParty
     */
    public function getBusinessParty()
    {
        return $this->container['business_party'];
    }

    /**
     * Sets business_party
     *
     * @param \SplititSdkClient\Model\BusinessParty $business_party business_party
     *
     * @return $this
     */
    public function setBusinessParty($business_party)
    {
        $this->container['business_party'] = $business_party;

        return $this;
    }

    /**
     * Gets config_keys
     *
     * @return \SplititSdkClient\Model\ConfigKeys[]
     */
    public function getConfigKeys()
    {
        return $this->container['config_keys'];
    }

    /**
     * Sets config_keys
     *
     * @param \SplititSdkClient\Model\ConfigKeys[] $config_keys config_keys
     *
     * @return $this
     */
    public function setConfigKeys($config_keys)
    {
        $this->container['config_keys'] = $config_keys;

        return $this;
    }

    /**
     * Gets versioned_touch_points
     *
     * @return \SplititSdkClient\Model\VersionedTouchPoints[]
     */
    public function getVersionedTouchPoints()
    {
        return $this->container['versioned_touch_points'];
    }

    /**
     * Sets versioned_touch_points
     *
     * @param \SplititSdkClient\Model\VersionedTouchPoints[] $versioned_touch_points versioned_touch_points
     *
     * @return $this
     */
    public function setVersionedTouchPoints($versioned_touch_points)
    {
        $this->container['versioned_touch_points'] = $versioned_touch_points;

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

