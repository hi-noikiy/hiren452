<?php

namespace MyZillion\InsuranceApi;

/**
 *  Client class to communicate with MyZillion API
 *
 *  @author Serfe SA <info@serfe.com>
 */
class Client implements \MyZillion\InsuranceApi\Api\InsuranceAPIInterface
{

    /**
     * Error messages
     */
    const ERR_BAD_STRUCTURE = "The data doesn't complains with an implemented structure.";
    const ERR_OFFER_ITEM_VALUE = "Error decoding the value of an item";
    const ERR_POLICY_ITEMS = "Error, item doesn't has all the required fields";
    const ERR_NO_ITEMS = "Error, the 'items' can't be empty.";

    /**
     * Timeouts
     */
    const TIMEOUT_OFFER = 10;
    const TIMEOUT_ORDER = 30;

    /**
     * @var array   API Credentials [ username => "", password => ""]
     */
    protected $credentials;

    /**
     * @var boolean  If true, we'll call production API instead of staging
     */
    protected $useProductionApi;

    /**
     * Constructor
     *
     * @param array $credentials
     * @param boolean $useProductionApi
     */
    public function __construct($credentials, $useProductionApi = false)
    {
        $this->credentials = $credentials;
        $this->useProductionApi = $useProductionApi;
    }

    /**
     * {@inheritdoc}
     */
    public function getOffer($data)
    {
        return $this->callEndpoint(self::OFFER_ENDPOINT, $data, self::TIMEOUT_OFFER);
    }

    /**
     * {@inheritdoc}
     */
    public function postOrder($data)
    {
        return $this->callEndpoint(self::ORDER_POST_ENDPOINT, $data, self::TIMEOUT_ORDER);
    }

    /**
     *
     * @param string $endpoint
     * @param array $data
     * @return array
     */
    protected function callEndpoint($endpoint, $data, $timeout = 30)
    {
        try {
            $url = $this->getEndpointUrl($endpoint);
            $client = $this->getHttpClient($url, $data, $timeout);
            $rawResponse = curl_exec($client);
            $rawResponse = substr($rawResponse, curl_getinfo($client, CURLINFO_HEADER_SIZE));

            // Throwing exception if isn't an ERROR
            if (curl_error($client)) {
                throw new \Exception(curl_error($client), curl_getinfo($client, CURLINFO_HTTP_CODE));
            }
            // or if it's just not an OK 200
            if (!in_array(curl_getinfo($client, CURLINFO_HTTP_CODE), [200, 201])) {
                throw new \Exception($rawResponse, curl_getinfo($client, CURLINFO_HTTP_CODE));
            }

            $response = json_decode($rawResponse, true);
            curl_close($client);
        } catch (\Exception $ex) {
            $msg = 'An error ocurred trying to sent the request. ' . $ex->getMessage();
            $response = [
                "errors" => [
                    "code" => $ex->getCode(),
                    "message" => $ex->getMessage(),
                    "request_sent" => 0
                ]
            ];
        }

        return $response;
    }

    /**
     * Initialize and return Http Client
     *
     * @param string $url
     * @param array $data
     * @return \CURL\resource
     */
    protected function getHttpClient($url, $data, $timeout = 30)
    {
        $client = curl_init();
        $json = json_encode($data);
        $headers = $this->getRequestHeaders();
        curl_setopt($client, CURLOPT_URL, $url);
        curl_setopt($client, CURLOPT_HEADER, $headers);
        curl_setopt($client, CURLOPT_POST, 1);
        curl_setopt($client, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($client, CURLOPT_FAILONERROR, false);
        curl_setopt($client, CURLOPT_ENCODING, '');
        curl_setopt($client, CURLOPT_USERPWD, $this->getUsername() . ":" . $this->getPassword());
        curl_setopt($client, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($client, CURLOPT_POSTFIELDS, $json);
        curl_setopt($client, CURLOPT_HTTP200ALIASES, (array) 400);
        curl_setopt($client, CURLOPT_HTTPHEADER, [
          "Content-Type: application/json"
        ]);

        return $client;
    }

    /**
     * Check if data sent to the endpoint is valid
     *
     * @param array $data
     * @return array
     */
    protected function isValidData($data)
    {
        /**
         * Validate data before sent request (required fields, field formats, etc)
         * If data is not valid return an array with all the encountered errors
         */

        $validationResult = [
            'isValid' => true,
            'errors' => []
        ];
        $endpoint = '';
        $required = [
            "offer"  => ["order_id", "zip_code", "items"],
            "order" => ["order_number", "binder_requested", "customer", "item_groups"]
        ];

        /**
         * Gets the endpoint from a predefined required fields
         */
        if ((count(array_intersect_key(array_flip($required["offer"]), $data)) === count($required["offer"]))) {
            $endpoint = 'Offer';
        } elseif (
          isset($data['order']) &&
          count(array_intersect_key(array_flip($required["order"]), $data["order"])) === count($required["order"])) {
            $endpoint = 'OrderPost';
        } else {
            $validationResult = [
                'isValid' => false,
                'errors' => [self::ERR_BAD_STRUCTURE]
            ];
        }

        /**
         * Call the validator depending the endpoint
         */
        if ($endpoint) {
            $validator = "validate{$endpoint}Schema";
            $validationResult = $this->$validator($data);
        }

        return $validationResult;
    }

    /**
     * Get Headers
     *
     * @return array
     */
    protected function getRequestHeaders()
    {
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ];

        return $headers;
    }

    /**
     * Return endpoint url
     *
     * @param string $endpoint
     * @return string
     */
    protected function getEndpointUrl($endpoint)
    {
        $baseUrl = self::STAGING_API_URL;
        if ($this->useProductionApi()) {
            $baseUrl = self::PRODUCTION_API_URL;
        }

        return $baseUrl . $endpoint;
    }

    /**
     * Get Username
     *
     * @return string
     */
    protected function getUsername()
    {
        if (!isset($this->credentials['username'])) {
            $this->parseApiKey();
        }

        return $this->credentials['username'];
    }

    /**
     * Get Password
     *
     * @return string
     */
    protected function getPassword()
    {
        if (!isset($this->credentials['password'])) {
            $this->parseApiKey();
        }

        return $this->credentials['password'];
    }

    /**
     * Parse the $credentials['api_key'] to retrieve and set the username and password credentials
     *
     * @return array
     */
    protected function parseApiKey()
    {
        if (isset($this->credentials['api_key']) && !empty($this->credentials['api_key'])) {
            $apiKey = $this->credentials['api_key'];
            try {
                $auth = trim(str_replace('Basic', '', @base64_decode($apiKey)));
                $credentials = explode(':', $auth);

                $this->credentials = [
                  'username' => $credentials[0],
                  'password' => $credentials[1],
                  'api_key' => $apiKey
                ];
            } catch (\Exception $e) {
                throw new \Exception("Error trying to parse API credentials. " . $e->getMessage(), 1);
            }
        } else {
            throw new \Exception("Error trying to parse API credentials. api_key is not set", 1);
        }
        return $this->credentials;
    }

    /**
     * Use Production API
     *
     * @return boolean
     */
    protected function useProductionApi()
    {
        return $this->useProductionApi;
    }

    /**
     * Check if the Offer is valid and return the errors array
     *
     * @param array $data
     * @return array
     */
    protected function validateOfferSchema($data)
    {
        $validationResult = [
            'isValid' => true,
            'errors' => []
        ];
        $baseStructure = [
            "order_id" => "string",
            "zip_code" => "string nullable",
            "items" => [
                [
                    "quantity" => "string numeric",
                    "value" => "string numeric",
                    "type" => "string"
                ]
            ]
        ];
        foreach ($data as $field => $value) {
            if (in_array($field, array_keys($baseStructure)) && (gettype($baseStructure[$field]) === gettype($value) || explode(" ", $baseStructure[$field])[0] === gettype($value))) {
                if (gettype($value) === "array") {
                    if (!empty($value)) {
                        foreach ($value as $key => $val) {
                            if (
                                !(
                                    count(array_intersect(array_keys($val), array_keys($baseStructure[$field][0]))) == count(array_keys($baseStructure[$field][0]))
                                && (gettype($val['value']) === explode(" ", $baseStructure[$field][0]['value'])[0])
                                && (explode(" ", $baseStructure[$field][0]['value'])[1] == "numeric" ? is_numeric($val['value']) : true)
                                )
                            ) {
                                $validationResult['errors'][] = self::ERR_OFFER_ITEM_VALUE;
                                break;
                            }
                        }
                    } else {
                        $validationResult['errors'][] = self::ERR_NO_ITEMS;
                    }
                }
                unset($baseStructure[$field]);
            }
        }
        foreach ($baseStructure as $key => $value) {
            if (in_array($key, array_keys($data))) {
                if (strpos($baseStructure[$key], 'null') === false) {
                    $validationResult['errors'][] = "Type for \"{$key}\" null or mismatch, it should be a \"{$baseStructure[$key]}\"";
                }
            } else {
                $validationResult['errors'][] = "The key \"{$key}\" isn't allowed";
            }
        }
        if (!empty($validationResult['errors'])) {
            $validationResult['isValid'] = false;
        }
        return $validationResult;
    }

    /**
     * Check if the Order Post data is valid and return the errors array
     *
     * @param array $data
     * @return array
     */
    protected function validateOrderPostSchema($data)
    {
        $validationResult['isValid'] = true;
        $structureOrderPost = [
            "order" => [
                "order_number" => "required string",
                "binder_requested"=> "required boolean",
                "customer" => [
                    "email" => "string",
                    "first_name" => "required string",
                    "last_name" => "required string",
                    "mobile_phone" => "string",
                    "billing_street" => "string",
                    "billing_city" => "string",
                    "billing_state" => "string",
                    "billing_zip" => "string"
                ],
                "item_groups" => [
                    [
                        "description" => "string",
                        "name" => "string",
                        "photo_link" => "string",
                        "items" => [
                            [
                                "type" => "required string",
                                "sku" => "string",
                                "certification_type" => "string",
                                "certification_number" => "string",
                                "photo_link" => "string",
                                "description_full" => "required string",
                                "description_short" => "required string",
                                "weight" => "string",
                                "purchase_price" => [
                                    "amount" => "required integer",
                                    "currency" => "required string"
                                ],
                                "estimated_value" => [
                                    "amount" => "required integer",
                                    "currency" => "required string"
                                ],
                                "serial_number" => "string",
                                "model_number" => "string"
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $validationResult['errors'] = $this->validateStructure($structureOrderPost, $data, ["item_groups","items"]);
        if (!empty($validationResult['errors'])) {
            $validationResult['isValid'] = false;
        }
        return $validationResult;
    }

    /**
     * Parse structure
     * [boolean, integer, double, string, array, object, resource, null, unknowntype ]
     * Additional [ requiere ]
     * https://www.php.net/manual/en/function.gettype.php
     *
     * @param array $structure Base data structure
     * @param array $dataType Data to verify
     * @param array $items Repeating data array within structure
     * @param array $errors
     * @param array $path Path where the error was detected
     * @return array
     */
    private function validateStructure($structure, $data, $items = [], $errors =[], $path = "")
    {
        return $errors;
        $errors = $errors;
        $items = $items;
        try {
            // I walk my structure
            foreach ($structure as $k => $v) {
                // I generate my auxiliaries structure
                $structureValue = $v;
                $structureType = (gettype($structureValue) === 'array') ? ['array'] : explode(" ", $structureValue);
                // Auxiliary to detect required
                $isRequiere = in_array('required', $structureType);
                // Check if it exists within the data
                if (!isset($data[$k])) {
                    if ($isRequiere) {
                        //In case it is required to launch a warning
                        $errors[]= "The path does not exist " . $path . "." . $k . "-" . $isRequiere;
                    }
                    continue;
                }
                // I generate my auxiliaries data
                $dataValue = $data[$k];
                $dataType = gettype($dataValue);

                // I verify that the data types match
                if (!in_array($dataType, $structureType)) {
                    // In case they are different ERROR
                    $path .= "." . $k;
                    $errors[] = "The element  " . $path . "  must be of the type [" . implode(", ", $structureType) . "] was received  [" . $dataType . "]";
                } else {
                    // In case they match
                    // I verify that it is not an array
                    if (in_array('array', $structureType)) {
                        // If it is array, I must reinject the new data
                        $path .= ($path!=="") ? "." : "";
                        //For item types, the same structure must be verified.
                        if (!in_array($k, $items)) {
                            // If it is not of the type items
                            $path .= $k;
                            $errors = $this->validateStructure($structureValue, $dataValue, $items, $errors, $path);
                        } else {
                            // If it is of the type items
                            // Same structure, different rows
                            foreach ($dataValue as $k2 => $v2) {
                                $path .= $k . "." . $k2;
                                $errors = $this->validateStructure($structureValue[0], $v2, $items, $errors, $path);
                            }
                        }
                    }
                }
            }
        } catch (\Exception $ex) {
            $errors[] = $ex->getCode() . ":" . $ex->getMessage();
        }
        return $errors;
    }
}
