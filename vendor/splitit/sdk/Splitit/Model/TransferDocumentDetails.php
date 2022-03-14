<?php
/**
 * TransferDocumentDetails
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
 * TransferDocumentDetails Class Doc Comment
 *
 * @category Class
 * @package  SplititSdkClient
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class TransferDocumentDetails implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'TransferDocumentDetails';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        'id' => 'int',
        'amount' => 'float',
        'transfer_reason' => 'int',
        'transfer_document_id' => 'int',
        'installment_id' => 'int',
        'plan_id' => 'int',
        'installment' => '\SplititSdkClient\Model\Installments',
        'plan' => '\SplititSdkClient\Model\InstallmentPlans',
        'transfer_document' => '\SplititSdkClient\Model\TransferDocuments'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerFormats = [
        'id' => 'int64',
        'amount' => 'decimal',
        'transfer_reason' => 'int32',
        'transfer_document_id' => 'int64',
        'installment_id' => 'int64',
        'plan_id' => 'int64',
        'installment' => null,
        'plan' => null,
        'transfer_document' => null
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
        'amount' => 'Amount',
        'transfer_reason' => 'TransferReason',
        'transfer_document_id' => 'TransferDocumentId',
        'installment_id' => 'InstallmentId',
        'plan_id' => 'PlanId',
        'installment' => 'Installment',
        'plan' => 'Plan',
        'transfer_document' => 'TransferDocument'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'id' => 'setId',
        'amount' => 'setAmount',
        'transfer_reason' => 'setTransferReason',
        'transfer_document_id' => 'setTransferDocumentId',
        'installment_id' => 'setInstallmentId',
        'plan_id' => 'setPlanId',
        'installment' => 'setInstallment',
        'plan' => 'setPlan',
        'transfer_document' => 'setTransferDocument'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'id' => 'getId',
        'amount' => 'getAmount',
        'transfer_reason' => 'getTransferReason',
        'transfer_document_id' => 'getTransferDocumentId',
        'installment_id' => 'getInstallmentId',
        'plan_id' => 'getPlanId',
        'installment' => 'getInstallment',
        'plan' => 'getPlan',
        'transfer_document' => 'getTransferDocument'
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
        $this->container['amount'] = isset($data['amount']) ? $data['amount'] : null;
        $this->container['transfer_reason'] = isset($data['transfer_reason']) ? $data['transfer_reason'] : null;
        $this->container['transfer_document_id'] = isset($data['transfer_document_id']) ? $data['transfer_document_id'] : null;
        $this->container['installment_id'] = isset($data['installment_id']) ? $data['installment_id'] : null;
        $this->container['plan_id'] = isset($data['plan_id']) ? $data['plan_id'] : null;
        $this->container['installment'] = isset($data['installment']) ? $data['installment'] : null;
        $this->container['plan'] = isset($data['plan']) ? $data['plan'] : null;
        $this->container['transfer_document'] = isset($data['transfer_document']) ? $data['transfer_document'] : null;
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
        if ($this->container['amount'] === null) {
            $invalidProperties[] = "'amount' can't be null";
        }
        if ($this->container['transfer_reason'] === null) {
            $invalidProperties[] = "'transfer_reason' can't be null";
        }
        if ($this->container['transfer_document_id'] === null) {
            $invalidProperties[] = "'transfer_document_id' can't be null";
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
     * Gets transfer_reason
     *
     * @return int
     */
    public function getTransferReason()
    {
        return $this->container['transfer_reason'];
    }

    /**
     * Sets transfer_reason
     *
     * @param int $transfer_reason transfer_reason
     *
     * @return $this
     */
    public function setTransferReason($transfer_reason)
    {
        $this->container['transfer_reason'] = $transfer_reason;

        return $this;
    }

    /**
     * Gets transfer_document_id
     *
     * @return int
     */
    public function getTransferDocumentId()
    {
        return $this->container['transfer_document_id'];
    }

    /**
     * Sets transfer_document_id
     *
     * @param int $transfer_document_id transfer_document_id
     *
     * @return $this
     */
    public function setTransferDocumentId($transfer_document_id)
    {
        $this->container['transfer_document_id'] = $transfer_document_id;

        return $this;
    }

    /**
     * Gets installment_id
     *
     * @return int
     */
    public function getInstallmentId()
    {
        return $this->container['installment_id'];
    }

    /**
     * Sets installment_id
     *
     * @param int $installment_id installment_id
     *
     * @return $this
     */
    public function setInstallmentId($installment_id)
    {
        $this->container['installment_id'] = $installment_id;

        return $this;
    }

    /**
     * Gets plan_id
     *
     * @return int
     */
    public function getPlanId()
    {
        return $this->container['plan_id'];
    }

    /**
     * Sets plan_id
     *
     * @param int $plan_id plan_id
     *
     * @return $this
     */
    public function setPlanId($plan_id)
    {
        $this->container['plan_id'] = $plan_id;

        return $this;
    }

    /**
     * Gets installment
     *
     * @return \SplititSdkClient\Model\Installments
     */
    public function getInstallment()
    {
        return $this->container['installment'];
    }

    /**
     * Sets installment
     *
     * @param \SplititSdkClient\Model\Installments $installment installment
     *
     * @return $this
     */
    public function setInstallment($installment)
    {
        $this->container['installment'] = $installment;

        return $this;
    }

    /**
     * Gets plan
     *
     * @return \SplititSdkClient\Model\InstallmentPlans
     */
    public function getPlan()
    {
        return $this->container['plan'];
    }

    /**
     * Sets plan
     *
     * @param \SplititSdkClient\Model\InstallmentPlans $plan plan
     *
     * @return $this
     */
    public function setPlan($plan)
    {
        $this->container['plan'] = $plan;

        return $this;
    }

    /**
     * Gets transfer_document
     *
     * @return \SplititSdkClient\Model\TransferDocuments
     */
    public function getTransferDocument()
    {
        return $this->container['transfer_document'];
    }

    /**
     * Sets transfer_document
     *
     * @param \SplititSdkClient\Model\TransferDocuments $transfer_document transfer_document
     *
     * @return $this
     */
    public function setTransferDocument($transfer_document)
    {
        $this->container['transfer_document'] = $transfer_document;

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

