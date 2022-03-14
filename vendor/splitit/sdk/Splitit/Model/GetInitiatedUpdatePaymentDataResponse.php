<?php
/**
 * GetInitiatedUpdatePaymentDataResponse
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
 * GetInitiatedUpdatePaymentDataResponse Class Doc Comment
 *
 * @category Class
 * @package  SplititSdkClient
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class GetInitiatedUpdatePaymentDataResponse implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'GetInitiatedUpdatePaymentDataResponse';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        'response_header' => '\SplititSdkClient\Model\ResponseHeader',
        'card_data' => '\SplititSdkClient\Model\CardData',
        'merchant' => '\SplititSdkClient\Model\MerchantRef',
        'redirect_urls' => '\SplititSdkClient\Model\RedirectUrls',
        'outstanding_amount' => '\SplititSdkClient\Model\MoneyWithCurrencyCode',
        'terms_and_conditions' => '\SplititSdkClient\Model\TermsAndConditions',
        'processor_name' => 'string',
        'is3_ds_required' => 'bool',
        'last_error' => '\SplititSdkClient\Model\Error',
        'logo' => 'string',
        'installment_plan_number' => 'string'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerFormats = [
        'response_header' => null,
        'card_data' => null,
        'merchant' => null,
        'redirect_urls' => null,
        'outstanding_amount' => null,
        'terms_and_conditions' => null,
        'processor_name' => null,
        'is3_ds_required' => null,
        'last_error' => null,
        'logo' => null,
        'installment_plan_number' => null
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
        'response_header' => 'ResponseHeader',
        'card_data' => 'CardData',
        'merchant' => 'Merchant',
        'redirect_urls' => 'RedirectUrls',
        'outstanding_amount' => 'OutstandingAmount',
        'terms_and_conditions' => 'TermsAndConditions',
        'processor_name' => 'ProcessorName',
        'is3_ds_required' => 'Is3DSRequired',
        'last_error' => 'LastError',
        'logo' => 'Logo',
        'installment_plan_number' => 'InstallmentPlanNumber'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'response_header' => 'setResponseHeader',
        'card_data' => 'setCardData',
        'merchant' => 'setMerchant',
        'redirect_urls' => 'setRedirectUrls',
        'outstanding_amount' => 'setOutstandingAmount',
        'terms_and_conditions' => 'setTermsAndConditions',
        'processor_name' => 'setProcessorName',
        'is3_ds_required' => 'setIs3DsRequired',
        'last_error' => 'setLastError',
        'logo' => 'setLogo',
        'installment_plan_number' => 'setInstallmentPlanNumber'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'response_header' => 'getResponseHeader',
        'card_data' => 'getCardData',
        'merchant' => 'getMerchant',
        'redirect_urls' => 'getRedirectUrls',
        'outstanding_amount' => 'getOutstandingAmount',
        'terms_and_conditions' => 'getTermsAndConditions',
        'processor_name' => 'getProcessorName',
        'is3_ds_required' => 'getIs3DsRequired',
        'last_error' => 'getLastError',
        'logo' => 'getLogo',
        'installment_plan_number' => 'getInstallmentPlanNumber'
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
        $this->container['response_header'] = isset($data['response_header']) ? $data['response_header'] : null;
        $this->container['card_data'] = isset($data['card_data']) ? $data['card_data'] : null;
        $this->container['merchant'] = isset($data['merchant']) ? $data['merchant'] : null;
        $this->container['redirect_urls'] = isset($data['redirect_urls']) ? $data['redirect_urls'] : null;
        $this->container['outstanding_amount'] = isset($data['outstanding_amount']) ? $data['outstanding_amount'] : null;
        $this->container['terms_and_conditions'] = isset($data['terms_and_conditions']) ? $data['terms_and_conditions'] : null;
        $this->container['processor_name'] = isset($data['processor_name']) ? $data['processor_name'] : null;
        $this->container['is3_ds_required'] = isset($data['is3_ds_required']) ? $data['is3_ds_required'] : null;
        $this->container['last_error'] = isset($data['last_error']) ? $data['last_error'] : null;
        $this->container['logo'] = isset($data['logo']) ? $data['logo'] : null;
        $this->container['installment_plan_number'] = isset($data['installment_plan_number']) ? $data['installment_plan_number'] : null;
    }

    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalidProperties = [];

        if ($this->container['is3_ds_required'] === null) {
            $invalidProperties[] = "'is3_ds_required' can't be null";
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
     * Gets card_data
     *
     * @return \SplititSdkClient\Model\CardData
     */
    public function getCardData()
    {
        return $this->container['card_data'];
    }

    /**
     * Sets card_data
     *
     * @param \SplititSdkClient\Model\CardData $card_data card_data
     *
     * @return $this
     */
    public function setCardData($card_data)
    {
        $this->container['card_data'] = $card_data;

        return $this;
    }

    /**
     * Gets merchant
     *
     * @return \SplititSdkClient\Model\MerchantRef
     */
    public function getMerchant()
    {
        return $this->container['merchant'];
    }

    /**
     * Sets merchant
     *
     * @param \SplititSdkClient\Model\MerchantRef $merchant merchant
     *
     * @return $this
     */
    public function setMerchant($merchant)
    {
        $this->container['merchant'] = $merchant;

        return $this;
    }

    /**
     * Gets redirect_urls
     *
     * @return \SplititSdkClient\Model\RedirectUrls
     */
    public function getRedirectUrls()
    {
        return $this->container['redirect_urls'];
    }

    /**
     * Sets redirect_urls
     *
     * @param \SplititSdkClient\Model\RedirectUrls $redirect_urls redirect_urls
     *
     * @return $this
     */
    public function setRedirectUrls($redirect_urls)
    {
        $this->container['redirect_urls'] = $redirect_urls;

        return $this;
    }

    /**
     * Gets outstanding_amount
     *
     * @return \SplititSdkClient\Model\MoneyWithCurrencyCode
     */
    public function getOutstandingAmount()
    {
        return $this->container['outstanding_amount'];
    }

    /**
     * Sets outstanding_amount
     *
     * @param \SplititSdkClient\Model\MoneyWithCurrencyCode $outstanding_amount outstanding_amount
     *
     * @return $this
     */
    public function setOutstandingAmount($outstanding_amount)
    {
        $this->container['outstanding_amount'] = $outstanding_amount;

        return $this;
    }

    /**
     * Gets terms_and_conditions
     *
     * @return \SplititSdkClient\Model\TermsAndConditions
     */
    public function getTermsAndConditions()
    {
        return $this->container['terms_and_conditions'];
    }

    /**
     * Sets terms_and_conditions
     *
     * @param \SplititSdkClient\Model\TermsAndConditions $terms_and_conditions terms_and_conditions
     *
     * @return $this
     */
    public function setTermsAndConditions($terms_and_conditions)
    {
        $this->container['terms_and_conditions'] = $terms_and_conditions;

        return $this;
    }

    /**
     * Gets processor_name
     *
     * @return string
     */
    public function getProcessorName()
    {
        return $this->container['processor_name'];
    }

    /**
     * Sets processor_name
     *
     * @param string $processor_name processor_name
     *
     * @return $this
     */
    public function setProcessorName($processor_name)
    {
        $this->container['processor_name'] = $processor_name;

        return $this;
    }

    /**
     * Gets is3_ds_required
     *
     * @return bool
     */
    public function getIs3DsRequired()
    {
        return $this->container['is3_ds_required'];
    }

    /**
     * Sets is3_ds_required
     *
     * @param bool $is3_ds_required is3_ds_required
     *
     * @return $this
     */
    public function setIs3DsRequired($is3_ds_required)
    {
        $this->container['is3_ds_required'] = $is3_ds_required;

        return $this;
    }

    /**
     * Gets last_error
     *
     * @return \SplititSdkClient\Model\Error
     */
    public function getLastError()
    {
        return $this->container['last_error'];
    }

    /**
     * Sets last_error
     *
     * @param \SplititSdkClient\Model\Error $last_error last_error
     *
     * @return $this
     */
    public function setLastError($last_error)
    {
        $this->container['last_error'] = $last_error;

        return $this;
    }

    /**
     * Gets logo
     *
     * @return string
     */
    public function getLogo()
    {
        return $this->container['logo'];
    }

    /**
     * Sets logo
     *
     * @param string $logo logo
     *
     * @return $this
     */
    public function setLogo($logo)
    {
        $this->container['logo'] = $logo;

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


