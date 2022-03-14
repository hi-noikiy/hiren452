<?php
/**
 * FeesDocuments
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
 * FeesDocuments Class Doc Comment
 *
 * @category Class
 * @package  SplititSdkClient
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class FeesDocuments implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'FeesDocuments';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        'id' => 'int',
        'fee_amount_in_effective_currency_amount' => 'float',
        'fee_amount_in_transaction_currency_amount' => 'float',
        'fee_amount_in_uniform_currency_amount' => 'float',
        'effective_currency_id' => 'int',
        'transaction_currency_id' => 'int',
        'uniform_currency_id' => 'int',
        'reference_fees_document_id' => 'int',
        'installment_id' => 'int',
        'plan_id' => 'int',
        'business_unit_id' => 'int',
        'effective_date_utc' => '\DateTime',
        'fee_rule_data_id' => 'int',
        'fee_rule_data' => '\SplititSdkClient\Model\FeesRuleDatas',
        'business_unit' => '\SplititSdkClient\Model\BusinessUnits',
        'effective_currency' => '\SplititSdkClient\Model\Currencies',
        'installment' => '\SplititSdkClient\Model\Installments',
        'plan' => '\SplititSdkClient\Model\InstallmentPlans',
        'reference_fees_document' => '\SplititSdkClient\Model\FeesDocuments',
        'transaction_currency' => '\SplititSdkClient\Model\Currencies',
        'uniform_currency' => '\SplititSdkClient\Model\Currencies',
        'inverse_reference_fees_document' => '\SplititSdkClient\Model\FeesDocuments[]',
        'fee_entity' => '\SplititSdkClient\Model\AccountingParty',
        'fee_type' => '\SplititSdkClient\Model\FeesTypes',
        'range_type' => '\SplititSdkClient\Model\RangeType',
        'fee_amount_in_effective_currency' => '\SplititSdkClient\Model\Money2',
        'fee_amount_in_transaction_currency' => '\SplititSdkClient\Model\Money2',
        'fee_amount_in_uniform_currency' => '\SplititSdkClient\Model\Money2'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerFormats = [
        'id' => 'int64',
        'fee_amount_in_effective_currency_amount' => 'decimal',
        'fee_amount_in_transaction_currency_amount' => 'decimal',
        'fee_amount_in_uniform_currency_amount' => 'decimal',
        'effective_currency_id' => 'int64',
        'transaction_currency_id' => 'int64',
        'uniform_currency_id' => 'int64',
        'reference_fees_document_id' => 'int64',
        'installment_id' => 'int64',
        'plan_id' => 'int64',
        'business_unit_id' => 'int64',
        'effective_date_utc' => 'date-time',
        'fee_rule_data_id' => 'int64',
        'fee_rule_data' => null,
        'business_unit' => null,
        'effective_currency' => null,
        'installment' => null,
        'plan' => null,
        'reference_fees_document' => null,
        'transaction_currency' => null,
        'uniform_currency' => null,
        'inverse_reference_fees_document' => null,
        'fee_entity' => null,
        'fee_type' => null,
        'range_type' => null,
        'fee_amount_in_effective_currency' => null,
        'fee_amount_in_transaction_currency' => null,
        'fee_amount_in_uniform_currency' => null
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
        'fee_amount_in_effective_currency_amount' => 'FeeAmountInEffectiveCurrencyAmount',
        'fee_amount_in_transaction_currency_amount' => 'FeeAmountInTransactionCurrencyAmount',
        'fee_amount_in_uniform_currency_amount' => 'FeeAmountInUniformCurrencyAmount',
        'effective_currency_id' => 'EffectiveCurrencyId',
        'transaction_currency_id' => 'TransactionCurrencyId',
        'uniform_currency_id' => 'UniformCurrencyId',
        'reference_fees_document_id' => 'ReferenceFeesDocumentId',
        'installment_id' => 'InstallmentId',
        'plan_id' => 'PlanId',
        'business_unit_id' => 'BusinessUnitId',
        'effective_date_utc' => 'EffectiveDateUtc',
        'fee_rule_data_id' => 'FeeRuleDataId',
        'fee_rule_data' => 'FeeRuleData',
        'business_unit' => 'BusinessUnit',
        'effective_currency' => 'EffectiveCurrency',
        'installment' => 'Installment',
        'plan' => 'Plan',
        'reference_fees_document' => 'ReferenceFeesDocument',
        'transaction_currency' => 'TransactionCurrency',
        'uniform_currency' => 'UniformCurrency',
        'inverse_reference_fees_document' => 'InverseReferenceFeesDocument',
        'fee_entity' => 'FeeEntity',
        'fee_type' => 'FeeType',
        'range_type' => 'RangeType',
        'fee_amount_in_effective_currency' => 'FeeAmountInEffectiveCurrency',
        'fee_amount_in_transaction_currency' => 'FeeAmountInTransactionCurrency',
        'fee_amount_in_uniform_currency' => 'FeeAmountInUniformCurrency'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'id' => 'setId',
        'fee_amount_in_effective_currency_amount' => 'setFeeAmountInEffectiveCurrencyAmount',
        'fee_amount_in_transaction_currency_amount' => 'setFeeAmountInTransactionCurrencyAmount',
        'fee_amount_in_uniform_currency_amount' => 'setFeeAmountInUniformCurrencyAmount',
        'effective_currency_id' => 'setEffectiveCurrencyId',
        'transaction_currency_id' => 'setTransactionCurrencyId',
        'uniform_currency_id' => 'setUniformCurrencyId',
        'reference_fees_document_id' => 'setReferenceFeesDocumentId',
        'installment_id' => 'setInstallmentId',
        'plan_id' => 'setPlanId',
        'business_unit_id' => 'setBusinessUnitId',
        'effective_date_utc' => 'setEffectiveDateUtc',
        'fee_rule_data_id' => 'setFeeRuleDataId',
        'fee_rule_data' => 'setFeeRuleData',
        'business_unit' => 'setBusinessUnit',
        'effective_currency' => 'setEffectiveCurrency',
        'installment' => 'setInstallment',
        'plan' => 'setPlan',
        'reference_fees_document' => 'setReferenceFeesDocument',
        'transaction_currency' => 'setTransactionCurrency',
        'uniform_currency' => 'setUniformCurrency',
        'inverse_reference_fees_document' => 'setInverseReferenceFeesDocument',
        'fee_entity' => 'setFeeEntity',
        'fee_type' => 'setFeeType',
        'range_type' => 'setRangeType',
        'fee_amount_in_effective_currency' => 'setFeeAmountInEffectiveCurrency',
        'fee_amount_in_transaction_currency' => 'setFeeAmountInTransactionCurrency',
        'fee_amount_in_uniform_currency' => 'setFeeAmountInUniformCurrency'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'id' => 'getId',
        'fee_amount_in_effective_currency_amount' => 'getFeeAmountInEffectiveCurrencyAmount',
        'fee_amount_in_transaction_currency_amount' => 'getFeeAmountInTransactionCurrencyAmount',
        'fee_amount_in_uniform_currency_amount' => 'getFeeAmountInUniformCurrencyAmount',
        'effective_currency_id' => 'getEffectiveCurrencyId',
        'transaction_currency_id' => 'getTransactionCurrencyId',
        'uniform_currency_id' => 'getUniformCurrencyId',
        'reference_fees_document_id' => 'getReferenceFeesDocumentId',
        'installment_id' => 'getInstallmentId',
        'plan_id' => 'getPlanId',
        'business_unit_id' => 'getBusinessUnitId',
        'effective_date_utc' => 'getEffectiveDateUtc',
        'fee_rule_data_id' => 'getFeeRuleDataId',
        'fee_rule_data' => 'getFeeRuleData',
        'business_unit' => 'getBusinessUnit',
        'effective_currency' => 'getEffectiveCurrency',
        'installment' => 'getInstallment',
        'plan' => 'getPlan',
        'reference_fees_document' => 'getReferenceFeesDocument',
        'transaction_currency' => 'getTransactionCurrency',
        'uniform_currency' => 'getUniformCurrency',
        'inverse_reference_fees_document' => 'getInverseReferenceFeesDocument',
        'fee_entity' => 'getFeeEntity',
        'fee_type' => 'getFeeType',
        'range_type' => 'getRangeType',
        'fee_amount_in_effective_currency' => 'getFeeAmountInEffectiveCurrency',
        'fee_amount_in_transaction_currency' => 'getFeeAmountInTransactionCurrency',
        'fee_amount_in_uniform_currency' => 'getFeeAmountInUniformCurrency'
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
        $this->container['fee_amount_in_effective_currency_amount'] = isset($data['fee_amount_in_effective_currency_amount']) ? $data['fee_amount_in_effective_currency_amount'] : null;
        $this->container['fee_amount_in_transaction_currency_amount'] = isset($data['fee_amount_in_transaction_currency_amount']) ? $data['fee_amount_in_transaction_currency_amount'] : null;
        $this->container['fee_amount_in_uniform_currency_amount'] = isset($data['fee_amount_in_uniform_currency_amount']) ? $data['fee_amount_in_uniform_currency_amount'] : null;
        $this->container['effective_currency_id'] = isset($data['effective_currency_id']) ? $data['effective_currency_id'] : null;
        $this->container['transaction_currency_id'] = isset($data['transaction_currency_id']) ? $data['transaction_currency_id'] : null;
        $this->container['uniform_currency_id'] = isset($data['uniform_currency_id']) ? $data['uniform_currency_id'] : null;
        $this->container['reference_fees_document_id'] = isset($data['reference_fees_document_id']) ? $data['reference_fees_document_id'] : null;
        $this->container['installment_id'] = isset($data['installment_id']) ? $data['installment_id'] : null;
        $this->container['plan_id'] = isset($data['plan_id']) ? $data['plan_id'] : null;
        $this->container['business_unit_id'] = isset($data['business_unit_id']) ? $data['business_unit_id'] : null;
        $this->container['effective_date_utc'] = isset($data['effective_date_utc']) ? $data['effective_date_utc'] : null;
        $this->container['fee_rule_data_id'] = isset($data['fee_rule_data_id']) ? $data['fee_rule_data_id'] : null;
        $this->container['fee_rule_data'] = isset($data['fee_rule_data']) ? $data['fee_rule_data'] : null;
        $this->container['business_unit'] = isset($data['business_unit']) ? $data['business_unit'] : null;
        $this->container['effective_currency'] = isset($data['effective_currency']) ? $data['effective_currency'] : null;
        $this->container['installment'] = isset($data['installment']) ? $data['installment'] : null;
        $this->container['plan'] = isset($data['plan']) ? $data['plan'] : null;
        $this->container['reference_fees_document'] = isset($data['reference_fees_document']) ? $data['reference_fees_document'] : null;
        $this->container['transaction_currency'] = isset($data['transaction_currency']) ? $data['transaction_currency'] : null;
        $this->container['uniform_currency'] = isset($data['uniform_currency']) ? $data['uniform_currency'] : null;
        $this->container['inverse_reference_fees_document'] = isset($data['inverse_reference_fees_document']) ? $data['inverse_reference_fees_document'] : null;
        $this->container['fee_entity'] = isset($data['fee_entity']) ? $data['fee_entity'] : null;
        $this->container['fee_type'] = isset($data['fee_type']) ? $data['fee_type'] : null;
        $this->container['range_type'] = isset($data['range_type']) ? $data['range_type'] : null;
        $this->container['fee_amount_in_effective_currency'] = isset($data['fee_amount_in_effective_currency']) ? $data['fee_amount_in_effective_currency'] : null;
        $this->container['fee_amount_in_transaction_currency'] = isset($data['fee_amount_in_transaction_currency']) ? $data['fee_amount_in_transaction_currency'] : null;
        $this->container['fee_amount_in_uniform_currency'] = isset($data['fee_amount_in_uniform_currency']) ? $data['fee_amount_in_uniform_currency'] : null;
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
        if ($this->container['fee_amount_in_effective_currency_amount'] === null) {
            $invalidProperties[] = "'fee_amount_in_effective_currency_amount' can't be null";
        }
        if ($this->container['fee_amount_in_transaction_currency_amount'] === null) {
            $invalidProperties[] = "'fee_amount_in_transaction_currency_amount' can't be null";
        }
        if ($this->container['fee_amount_in_uniform_currency_amount'] === null) {
            $invalidProperties[] = "'fee_amount_in_uniform_currency_amount' can't be null";
        }
        if ($this->container['effective_currency_id'] === null) {
            $invalidProperties[] = "'effective_currency_id' can't be null";
        }
        if ($this->container['uniform_currency_id'] === null) {
            $invalidProperties[] = "'uniform_currency_id' can't be null";
        }
        if ($this->container['effective_date_utc'] === null) {
            $invalidProperties[] = "'effective_date_utc' can't be null";
        }
        if ($this->container['fee_entity'] === null) {
            $invalidProperties[] = "'fee_entity' can't be null";
        }
        if ($this->container['fee_type'] === null) {
            $invalidProperties[] = "'fee_type' can't be null";
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
     * Gets fee_amount_in_effective_currency_amount
     *
     * @return float
     */
    public function getFeeAmountInEffectiveCurrencyAmount()
    {
        return $this->container['fee_amount_in_effective_currency_amount'];
    }

    /**
     * Sets fee_amount_in_effective_currency_amount
     *
     * @param float $fee_amount_in_effective_currency_amount fee_amount_in_effective_currency_amount
     *
     * @return $this
     */
    public function setFeeAmountInEffectiveCurrencyAmount($fee_amount_in_effective_currency_amount)
    {
        $this->container['fee_amount_in_effective_currency_amount'] = $fee_amount_in_effective_currency_amount;

        return $this;
    }

    /**
     * Gets fee_amount_in_transaction_currency_amount
     *
     * @return float
     */
    public function getFeeAmountInTransactionCurrencyAmount()
    {
        return $this->container['fee_amount_in_transaction_currency_amount'];
    }

    /**
     * Sets fee_amount_in_transaction_currency_amount
     *
     * @param float $fee_amount_in_transaction_currency_amount fee_amount_in_transaction_currency_amount
     *
     * @return $this
     */
    public function setFeeAmountInTransactionCurrencyAmount($fee_amount_in_transaction_currency_amount)
    {
        $this->container['fee_amount_in_transaction_currency_amount'] = $fee_amount_in_transaction_currency_amount;

        return $this;
    }

    /**
     * Gets fee_amount_in_uniform_currency_amount
     *
     * @return float
     */
    public function getFeeAmountInUniformCurrencyAmount()
    {
        return $this->container['fee_amount_in_uniform_currency_amount'];
    }

    /**
     * Sets fee_amount_in_uniform_currency_amount
     *
     * @param float $fee_amount_in_uniform_currency_amount fee_amount_in_uniform_currency_amount
     *
     * @return $this
     */
    public function setFeeAmountInUniformCurrencyAmount($fee_amount_in_uniform_currency_amount)
    {
        $this->container['fee_amount_in_uniform_currency_amount'] = $fee_amount_in_uniform_currency_amount;

        return $this;
    }

    /**
     * Gets effective_currency_id
     *
     * @return int
     */
    public function getEffectiveCurrencyId()
    {
        return $this->container['effective_currency_id'];
    }

    /**
     * Sets effective_currency_id
     *
     * @param int $effective_currency_id effective_currency_id
     *
     * @return $this
     */
    public function setEffectiveCurrencyId($effective_currency_id)
    {
        $this->container['effective_currency_id'] = $effective_currency_id;

        return $this;
    }

    /**
     * Gets transaction_currency_id
     *
     * @return int
     */
    public function getTransactionCurrencyId()
    {
        return $this->container['transaction_currency_id'];
    }

    /**
     * Sets transaction_currency_id
     *
     * @param int $transaction_currency_id transaction_currency_id
     *
     * @return $this
     */
    public function setTransactionCurrencyId($transaction_currency_id)
    {
        $this->container['transaction_currency_id'] = $transaction_currency_id;

        return $this;
    }

    /**
     * Gets uniform_currency_id
     *
     * @return int
     */
    public function getUniformCurrencyId()
    {
        return $this->container['uniform_currency_id'];
    }

    /**
     * Sets uniform_currency_id
     *
     * @param int $uniform_currency_id uniform_currency_id
     *
     * @return $this
     */
    public function setUniformCurrencyId($uniform_currency_id)
    {
        $this->container['uniform_currency_id'] = $uniform_currency_id;

        return $this;
    }

    /**
     * Gets reference_fees_document_id
     *
     * @return int
     */
    public function getReferenceFeesDocumentId()
    {
        return $this->container['reference_fees_document_id'];
    }

    /**
     * Sets reference_fees_document_id
     *
     * @param int $reference_fees_document_id reference_fees_document_id
     *
     * @return $this
     */
    public function setReferenceFeesDocumentId($reference_fees_document_id)
    {
        $this->container['reference_fees_document_id'] = $reference_fees_document_id;

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
     * Gets business_unit_id
     *
     * @return int
     */
    public function getBusinessUnitId()
    {
        return $this->container['business_unit_id'];
    }

    /**
     * Sets business_unit_id
     *
     * @param int $business_unit_id business_unit_id
     *
     * @return $this
     */
    public function setBusinessUnitId($business_unit_id)
    {
        $this->container['business_unit_id'] = $business_unit_id;

        return $this;
    }

    /**
     * Gets effective_date_utc
     *
     * @return \DateTime
     */
    public function getEffectiveDateUtc()
    {
        return $this->container['effective_date_utc'];
    }

    /**
     * Sets effective_date_utc
     *
     * @param \DateTime $effective_date_utc effective_date_utc
     *
     * @return $this
     */
    public function setEffectiveDateUtc($effective_date_utc)
    {
        $this->container['effective_date_utc'] = $effective_date_utc;

        return $this;
    }

    /**
     * Gets fee_rule_data_id
     *
     * @return int
     */
    public function getFeeRuleDataId()
    {
        return $this->container['fee_rule_data_id'];
    }

    /**
     * Sets fee_rule_data_id
     *
     * @param int $fee_rule_data_id fee_rule_data_id
     *
     * @return $this
     */
    public function setFeeRuleDataId($fee_rule_data_id)
    {
        $this->container['fee_rule_data_id'] = $fee_rule_data_id;

        return $this;
    }

    /**
     * Gets fee_rule_data
     *
     * @return \SplititSdkClient\Model\FeesRuleDatas
     */
    public function getFeeRuleData()
    {
        return $this->container['fee_rule_data'];
    }

    /**
     * Sets fee_rule_data
     *
     * @param \SplititSdkClient\Model\FeesRuleDatas $fee_rule_data fee_rule_data
     *
     * @return $this
     */
    public function setFeeRuleData($fee_rule_data)
    {
        $this->container['fee_rule_data'] = $fee_rule_data;

        return $this;
    }

    /**
     * Gets business_unit
     *
     * @return \SplititSdkClient\Model\BusinessUnits
     */
    public function getBusinessUnit()
    {
        return $this->container['business_unit'];
    }

    /**
     * Sets business_unit
     *
     * @param \SplititSdkClient\Model\BusinessUnits $business_unit business_unit
     *
     * @return $this
     */
    public function setBusinessUnit($business_unit)
    {
        $this->container['business_unit'] = $business_unit;

        return $this;
    }

    /**
     * Gets effective_currency
     *
     * @return \SplititSdkClient\Model\Currencies
     */
    public function getEffectiveCurrency()
    {
        return $this->container['effective_currency'];
    }

    /**
     * Sets effective_currency
     *
     * @param \SplititSdkClient\Model\Currencies $effective_currency effective_currency
     *
     * @return $this
     */
    public function setEffectiveCurrency($effective_currency)
    {
        $this->container['effective_currency'] = $effective_currency;

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
     * Gets reference_fees_document
     *
     * @return \SplititSdkClient\Model\FeesDocuments
     */
    public function getReferenceFeesDocument()
    {
        return $this->container['reference_fees_document'];
    }

    /**
     * Sets reference_fees_document
     *
     * @param \SplititSdkClient\Model\FeesDocuments $reference_fees_document reference_fees_document
     *
     * @return $this
     */
    public function setReferenceFeesDocument($reference_fees_document)
    {
        $this->container['reference_fees_document'] = $reference_fees_document;

        return $this;
    }

    /**
     * Gets transaction_currency
     *
     * @return \SplititSdkClient\Model\Currencies
     */
    public function getTransactionCurrency()
    {
        return $this->container['transaction_currency'];
    }

    /**
     * Sets transaction_currency
     *
     * @param \SplititSdkClient\Model\Currencies $transaction_currency transaction_currency
     *
     * @return $this
     */
    public function setTransactionCurrency($transaction_currency)
    {
        $this->container['transaction_currency'] = $transaction_currency;

        return $this;
    }

    /**
     * Gets uniform_currency
     *
     * @return \SplititSdkClient\Model\Currencies
     */
    public function getUniformCurrency()
    {
        return $this->container['uniform_currency'];
    }

    /**
     * Sets uniform_currency
     *
     * @param \SplititSdkClient\Model\Currencies $uniform_currency uniform_currency
     *
     * @return $this
     */
    public function setUniformCurrency($uniform_currency)
    {
        $this->container['uniform_currency'] = $uniform_currency;

        return $this;
    }

    /**
     * Gets inverse_reference_fees_document
     *
     * @return \SplititSdkClient\Model\FeesDocuments[]
     */
    public function getInverseReferenceFeesDocument()
    {
        return $this->container['inverse_reference_fees_document'];
    }

    /**
     * Sets inverse_reference_fees_document
     *
     * @param \SplititSdkClient\Model\FeesDocuments[] $inverse_reference_fees_document inverse_reference_fees_document
     *
     * @return $this
     */
    public function setInverseReferenceFeesDocument($inverse_reference_fees_document)
    {
        $this->container['inverse_reference_fees_document'] = $inverse_reference_fees_document;

        return $this;
    }

    /**
     * Gets fee_entity
     *
     * @return \SplititSdkClient\Model\AccountingParty
     */
    public function getFeeEntity()
    {
        return $this->container['fee_entity'];
    }

    /**
     * Sets fee_entity
     *
     * @param \SplititSdkClient\Model\AccountingParty $fee_entity fee_entity
     *
     * @return $this
     */
    public function setFeeEntity($fee_entity)
    {
        $this->container['fee_entity'] = $fee_entity;

        return $this;
    }

    /**
     * Gets fee_type
     *
     * @return \SplititSdkClient\Model\FeesTypes
     */
    public function getFeeType()
    {
        return $this->container['fee_type'];
    }

    /**
     * Sets fee_type
     *
     * @param \SplititSdkClient\Model\FeesTypes $fee_type fee_type
     *
     * @return $this
     */
    public function setFeeType($fee_type)
    {
        $this->container['fee_type'] = $fee_type;

        return $this;
    }

    /**
     * Gets range_type
     *
     * @return \SplititSdkClient\Model\RangeType
     */
    public function getRangeType()
    {
        return $this->container['range_type'];
    }

    /**
     * Sets range_type
     *
     * @param \SplititSdkClient\Model\RangeType $range_type range_type
     *
     * @return $this
     */
    public function setRangeType($range_type)
    {
        $this->container['range_type'] = $range_type;

        return $this;
    }

    /**
     * Gets fee_amount_in_effective_currency
     *
     * @return \SplititSdkClient\Model\Money2
     */
    public function getFeeAmountInEffectiveCurrency()
    {
        return $this->container['fee_amount_in_effective_currency'];
    }

    /**
     * Sets fee_amount_in_effective_currency
     *
     * @param \SplititSdkClient\Model\Money2 $fee_amount_in_effective_currency fee_amount_in_effective_currency
     *
     * @return $this
     */
    public function setFeeAmountInEffectiveCurrency($fee_amount_in_effective_currency)
    {
        $this->container['fee_amount_in_effective_currency'] = $fee_amount_in_effective_currency;

        return $this;
    }

    /**
     * Gets fee_amount_in_transaction_currency
     *
     * @return \SplititSdkClient\Model\Money2
     */
    public function getFeeAmountInTransactionCurrency()
    {
        return $this->container['fee_amount_in_transaction_currency'];
    }

    /**
     * Sets fee_amount_in_transaction_currency
     *
     * @param \SplititSdkClient\Model\Money2 $fee_amount_in_transaction_currency fee_amount_in_transaction_currency
     *
     * @return $this
     */
    public function setFeeAmountInTransactionCurrency($fee_amount_in_transaction_currency)
    {
        $this->container['fee_amount_in_transaction_currency'] = $fee_amount_in_transaction_currency;

        return $this;
    }

    /**
     * Gets fee_amount_in_uniform_currency
     *
     * @return \SplititSdkClient\Model\Money2
     */
    public function getFeeAmountInUniformCurrency()
    {
        return $this->container['fee_amount_in_uniform_currency'];
    }

    /**
     * Sets fee_amount_in_uniform_currency
     *
     * @param \SplititSdkClient\Model\Money2 $fee_amount_in_uniform_currency fee_amount_in_uniform_currency
     *
     * @return $this
     */
    public function setFeeAmountInUniformCurrency($fee_amount_in_uniform_currency)
    {
        $this->container['fee_amount_in_uniform_currency'] = $fee_amount_in_uniform_currency;

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


