<?php
/**
 * CreateInstallmentPlanLegacyResponse
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
 * CreateInstallmentPlanLegacyResponse Class Doc Comment
 *
 * @category Class
 * @package  SplititSdkClient
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class CreateInstallmentPlanLegacyResponse implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'CreateInstallmentPlanLegacyResponse';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        'api_key' => 'string',
        'installment_plan_status' => 'int',
        'result' => 'int',
        'payment_gateway' => 'string',
        'email' => 'string',
        'consumer_full_name' => 'string',
        'param_x' => 'string',
        'installment_number' => 'int',
        'amount' => 'float',
        'currency_name' => 'string',
        'currency_symbol' => 'string',
        'installment_plan_number' => 'string',
        'response_header' => '\SplititSdkClient\Model\ResponseHeader'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerFormats = [
        'api_key' => null,
        'installment_plan_status' => 'int32',
        'result' => 'int32',
        'payment_gateway' => null,
        'email' => null,
        'consumer_full_name' => null,
        'param_x' => null,
        'installment_number' => 'int32',
        'amount' => 'decimal',
        'currency_name' => null,
        'currency_symbol' => null,
        'installment_plan_number' => null,
        'response_header' => null
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
        'api_key' => 'ApiKey',
        'installment_plan_status' => 'InstallmentPlanStatus',
        'result' => 'Result',
        'payment_gateway' => 'PaymentGateway',
        'email' => 'Email',
        'consumer_full_name' => 'ConsumerFullName',
        'param_x' => 'ParamX',
        'installment_number' => 'InstallmentNumber',
        'amount' => 'Amount',
        'currency_name' => 'CurrencyName',
        'currency_symbol' => 'CurrencySymbol',
        'installment_plan_number' => 'InstallmentPlanNumber',
        'response_header' => 'ResponseHeader'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'api_key' => 'setApiKey',
        'installment_plan_status' => 'setInstallmentPlanStatus',
        'result' => 'setResult',
        'payment_gateway' => 'setPaymentGateway',
        'email' => 'setEmail',
        'consumer_full_name' => 'setConsumerFullName',
        'param_x' => 'setParamX',
        'installment_number' => 'setInstallmentNumber',
        'amount' => 'setAmount',
        'currency_name' => 'setCurrencyName',
        'currency_symbol' => 'setCurrencySymbol',
        'installment_plan_number' => 'setInstallmentPlanNumber',
        'response_header' => 'setResponseHeader'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'api_key' => 'getApiKey',
        'installment_plan_status' => 'getInstallmentPlanStatus',
        'result' => 'getResult',
        'payment_gateway' => 'getPaymentGateway',
        'email' => 'getEmail',
        'consumer_full_name' => 'getConsumerFullName',
        'param_x' => 'getParamX',
        'installment_number' => 'getInstallmentNumber',
        'amount' => 'getAmount',
        'currency_name' => 'getCurrencyName',
        'currency_symbol' => 'getCurrencySymbol',
        'installment_plan_number' => 'getInstallmentPlanNumber',
        'response_header' => 'getResponseHeader'
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
        $this->container['api_key'] = isset($data['api_key']) ? $data['api_key'] : null;
        $this->container['installment_plan_status'] = isset($data['installment_plan_status']) ? $data['installment_plan_status'] : null;
        $this->container['result'] = isset($data['result']) ? $data['result'] : null;
        $this->container['payment_gateway'] = isset($data['payment_gateway']) ? $data['payment_gateway'] : null;
        $this->container['email'] = isset($data['email']) ? $data['email'] : null;
        $this->container['consumer_full_name'] = isset($data['consumer_full_name']) ? $data['consumer_full_name'] : null;
        $this->container['param_x'] = isset($data['param_x']) ? $data['param_x'] : null;
        $this->container['installment_number'] = isset($data['installment_number']) ? $data['installment_number'] : null;
        $this->container['amount'] = isset($data['amount']) ? $data['amount'] : null;
        $this->container['currency_name'] = isset($data['currency_name']) ? $data['currency_name'] : null;
        $this->container['currency_symbol'] = isset($data['currency_symbol']) ? $data['currency_symbol'] : null;
        $this->container['installment_plan_number'] = isset($data['installment_plan_number']) ? $data['installment_plan_number'] : null;
        $this->container['response_header'] = isset($data['response_header']) ? $data['response_header'] : null;
    }

    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalidProperties = [];

        if ($this->container['installment_plan_status'] === null) {
            $invalidProperties[] = "'installment_plan_status' can't be null";
        }
        if ($this->container['result'] === null) {
            $invalidProperties[] = "'result' can't be null";
        }
        if ($this->container['installment_number'] === null) {
            $invalidProperties[] = "'installment_number' can't be null";
        }
        if ($this->container['amount'] === null) {
            $invalidProperties[] = "'amount' can't be null";
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
     * Gets api_key
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->container['api_key'];
    }

    /**
     * Sets api_key
     *
     * @param string $api_key api_key
     *
     * @return $this
     */
    public function setApiKey($api_key)
    {
        $this->container['api_key'] = $api_key;

        return $this;
    }

    /**
     * Gets installment_plan_status
     *
     * @return int
     */
    public function getInstallmentPlanStatus()
    {
        return $this->container['installment_plan_status'];
    }

    /**
     * Sets installment_plan_status
     *
     * @param int $installment_plan_status installment_plan_status
     *
     * @return $this
     */
    public function setInstallmentPlanStatus($installment_plan_status)
    {
        $this->container['installment_plan_status'] = $installment_plan_status;

        return $this;
    }

    /**
     * Gets result
     *
     * @return int
     */
    public function getResult()
    {
        return $this->container['result'];
    }

    /**
     * Sets result
     *
     * @param int $result result
     *
     * @return $this
     */
    public function setResult($result)
    {
        $this->container['result'] = $result;

        return $this;
    }

    /**
     * Gets payment_gateway
     *
     * @return string
     */
    public function getPaymentGateway()
    {
        return $this->container['payment_gateway'];
    }

    /**
     * Sets payment_gateway
     *
     * @param string $payment_gateway payment_gateway
     *
     * @return $this
     */
    public function setPaymentGateway($payment_gateway)
    {
        $this->container['payment_gateway'] = $payment_gateway;

        return $this;
    }

    /**
     * Gets email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->container['email'];
    }

    /**
     * Sets email
     *
     * @param string $email email
     *
     * @return $this
     */
    public function setEmail($email)
    {
        $this->container['email'] = $email;

        return $this;
    }

    /**
     * Gets consumer_full_name
     *
     * @return string
     */
    public function getConsumerFullName()
    {
        return $this->container['consumer_full_name'];
    }

    /**
     * Sets consumer_full_name
     *
     * @param string $consumer_full_name consumer_full_name
     *
     * @return $this
     */
    public function setConsumerFullName($consumer_full_name)
    {
        $this->container['consumer_full_name'] = $consumer_full_name;

        return $this;
    }

    /**
     * Gets param_x
     *
     * @return string
     */
    public function getParamX()
    {
        return $this->container['param_x'];
    }

    /**
     * Sets param_x
     *
     * @param string $param_x param_x
     *
     * @return $this
     */
    public function setParamX($param_x)
    {
        $this->container['param_x'] = $param_x;

        return $this;
    }

    /**
     * Gets installment_number
     *
     * @return int
     */
    public function getInstallmentNumber()
    {
        return $this->container['installment_number'];
    }

    /**
     * Sets installment_number
     *
     * @param int $installment_number installment_number
     *
     * @return $this
     */
    public function setInstallmentNumber($installment_number)
    {
        $this->container['installment_number'] = $installment_number;

        return $this;
    }

    /**
     * Gets amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->container['amount'];
    }

    /**
     * Sets amount
     *
     * @param float $amount amount
     *
     * @return $this
     */
    public function setAmount($amount)
    {
        $this->container['amount'] = $amount;

        return $this;
    }

    /**
     * Gets currency_name
     *
     * @return string
     */
    public function getCurrencyName()
    {
        return $this->container['currency_name'];
    }

    /**
     * Sets currency_name
     *
     * @param string $currency_name currency_name
     *
     * @return $this
     */
    public function setCurrencyName($currency_name)
    {
        $this->container['currency_name'] = $currency_name;

        return $this;
    }

    /**
     * Gets currency_symbol
     *
     * @return string
     */
    public function getCurrencySymbol()
    {
        return $this->container['currency_symbol'];
    }

    /**
     * Sets currency_symbol
     *
     * @param string $currency_symbol currency_symbol
     *
     * @return $this
     */
    public function setCurrencySymbol($currency_symbol)
    {
        $this->container['currency_symbol'] = $currency_symbol;

        return $this;
    }

    /**
     * Gets installment_plan_number
     *
     * @return string
     */
    public function getInstallmentPlanNumber()
    {
        return $this->container['installment_plan_number'];
    }

    /**
     * Sets installment_plan_number
     *
     * @param string $installment_plan_number installment_plan_number
     *
     * @return $this
     */
    public function setInstallmentPlanNumber($installment_plan_number)
    {
        $this->container['installment_plan_number'] = $installment_plan_number;

        return $this;
    }

    /**
     * Gets response_header
     *
     * @return \SplititSdkClient\Model\ResponseHeader
     */
    public function getResponseHeader()
    {
        return $this->container['response_header'];
    }

    /**
     * Sets response_header
     *
     * @param \SplititSdkClient\Model\ResponseHeader $response_header response_header
     *
     * @return $this
     */
    public function setResponseHeader($response_header)
    {
        $this->container['response_header'] = $response_header;

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


