<?php

namespace MyZillion\InsuranceApi;

use PHPUnit\Framework\TestCase as TestCase;

/**
 *  ClientTest class for testing the MyZillion API Client
 *
 *  @author Serfe SA <info@serfe.com>
 */
class ClientTest extends TestCase
{
    const USE_PRODUCTION = false;

    /**
     * @var \MyZillion\InsuranceApi\Client class under test
     */
    protected $client;

    /**
     * @var string sample order id
     */
    protected $orderID;

    /**
     * Phpunit setup.
     */
    public function setUp()
    {
        // Setting up credentials from environment variables
        $credentials = [
            'api_key' => getenv('MYZILLION_API_KEY'),
        ];

        // instantiate class under test
        $this->client = new Client($credentials, self::USE_PRODUCTION);
        $this->orderID = 'TT' . rand(10, 90) . rand(10, 90) . rand(10, 90) . rand(10, 90) . rand(10, 90);
    }

    /**
     * @test
     */
    public function testBadCredentials()
    {
        $username = 'IDontExists';
        $password = 'this-password-is-invalid-and-should-fail';
        $apiKey = @base64_encode('Basic ' . $username . ':' . $password);
        $badCredentials = [
            'api_key' => $apiKey
        ];

        $offer = $this->getSampleDataFor('offer');
        $expectedCode = 401;

        // Init a testing client
        $testClient = new Client($badCredentials, self::USE_PRODUCTION);
        // Making an offer call
        $response = $testClient->getOffer($offer);

        // Testing type and structure is correct
        $this->assertInternalType("array", $response);
        $this->assertArrayHasKey("errors", $response);
        $this->assertArrayHasKey("code", $response["errors"]);
        // In the response code is the $expectedCode
        $this->assertEquals($response["errors"]["code"], $expectedCode);
    }

    /**
     * @test
     */
    public function testGetOfferWithProperData()
    {
        $offer = $this->getSampleDataFor('offer');
        $response = $this->client->getOffer($offer);

        // Testing type is correct
        $this->assertInternalType("array", $response);
        // Testing response has the corresponding body
        $this->assertArrayHasKey("offer", $response);
        // Testing items
        // order_id
        $this->assertArrayHasKey("order_id", $response["offer"]);
        // zillion_customer_anonymous_id
        $this->assertArrayHasKey("zillion_customer_anonymous_id", $response["offer"]);
        // zillion_total_price
        $this->assertArrayHasKey("zillion_total_price", $response["offer"]);
    }

    /**
     * @test
     */
    public function testGetOfferWithWrongZipCode()
    {
        $offer = $this->getSampleDataFor('bad-offer-zip-code');
        $response = $this->client->getOffer($offer);

        $expectedCode = 400;

        // Testing type
        $this->assertInternalType("array", $response);
        // Response has to contains error
        $this->assertArrayHasKey("errors", $response);
        // Errors can't be empty
        $this->assertFalse(empty($response["errors"]));
        // In the response code is the $expectedCode
        $this->assertEquals($response["errors"]["code"], $expectedCode);
        // Among the errors we expect the intended one exists
        $this->assertContains("No Rate Found for Postal Code", $response["errors"]["message"]);
    }

    /**
     * @test
     */
    public function testGetOfferWithBadData()
    {
        $offer = $this->getSampleDataFor('bad-offer');
        $response = $this->client->getOffer($offer);

        // Testing type
        $this->assertInternalType("array", $response);
        // Response has to contains error
        $this->assertArrayHasKey("errors", $response);
        // Errors can't be empty
        $this->assertFalse(empty($response["errors"]));
        // Among the errors we expect the intended one exists
        $this->assertContains(Client::ERR_OFFER_ITEM_VALUE, $response["errors"]);
    }

    /**
     * @test
     */
    public function testGetOfferWithEmptyItemsData()
    {
        $offer = $this->getSampleDataFor('bad-offer-empty-items');
        $response = $this->client->getOffer($offer);

        // Testing type
        $this->assertInternalType("array", $response);
        // Response has to contains error
        $this->assertArrayHasKey("errors", $response);
        // Errors can't be empty
        $this->assertFalse(empty($response["errors"]));
        // Among the errors we expect the intended one exists
        $this->assertContains(Client::ERR_NO_ITEMS, $response["errors"]);
    }

    /**
     * @test
     */
    public function testOrderPostWithProperData()
    {
        $orderPost = $this->getSampleDataFor('order-post');
        $response = $this->client->postOrder($orderPost);
        // Testing response is correct
        $this->assertContains("Order created", $response);
    }

    /**
     * @test
     */
    public function testOrderPostWithBadData()
    {
        $policy = $this->getSampleDataFor('bad-post-order-empty-items');
        $response = $this->client->postOrder($policy);
        $this->assertEquals($response["errors"]["code"], 400);
        $this->assertContains("can't be blank", $response["errors"]["message"]);
    }

    protected function getSampleDataFor($value)
    {
        switch ($value) {
            case "offer":
                $result = [
                        "order_id" => $this->orderID,
                        "zip_code" => "85705",
                        "items" => [
                                    [
                                        "quantity" => 1,
                                        "type" => "bracelet",
                                        "value" => "1111.11"
                                    ],
                                    [
                                        "quantity" => 2,
                                        "type" => "necklace",
                                        "value" => "2222.22"
                                    ],
                                    [
                                        "quantity" => 1,
                                        "type" => "engament ring",
                                        "value" => "5555.55"
                                    ],

                        ]
                ];
                break;
            case "bad-offer":
                $result = [
                        "order_id" => $this->orderID,
                        "zip_code" => "85705",
                        "items" => [
                                    [
                                        "quantity" => 1,
                                        "value" => "wrong value"
                                    ],
                                    [
                                        "quantity" => 2,
                                        "type" => "necklace",
                                    ],
                                    [
                                        "type" => "engament ring",
                                        "value" => "5555.55"
                                    ],

                        ]
                ];
                break;

            case "bad-offer-zip-code":
                $result = [
                        "order_id" => $this->orderID,
                        "zip_code" => "98765",
                        "items" => [
                                    [
                                        "quantity" => 1,
                                        "type" => "bracelet",
                                        "value" => "1111.11"
                                    ],
                                    [
                                        "quantity" => 2,
                                        "type" => "necklace",
                                        "value" => "2222.22"
                                    ],
                                    [
                                        "quantity" => 1,
                                        "type" => "engament ring",
                                        "value" => "5555.55"
                                    ],

                        ]
                ];
                break;
            case "bad-offer-empty-items":
                $result = [
                        "order_id" => $this->orderID,
                        "zip_code" => "85705",
                        "items" => []
                ];
                break;
            case "order-post":
                $result = [
                    "order" => [
                      "order_number" => "1020304",
                      "binder_requested" => true,
                      "customer" => [
                          "email" => "customer@test.com",
                          "first_name" => "Dwight",
                          "last_name" => "Schrute",
                          "mobile_phone" => "123-123-1234",
                          "billing_street" => "785 E Dragram",
                          "billing_city" => "Tucson",
                          "billing_state" => "Arizona",
                          "billing_zip" => "85705",
                      ],
                      "item_groups" => [
                          [
                              "description" => "Some diamond description",
                              "name" => "Some diamond",
                              "photo_link" => "https://cdn.com/finished-item-image.png",
                              "items" => [
                                  [
                                    "quantity" => 2,
                                    "type"=> "bracelet",
                                    "sku" => "194s9f94",
                                    "certification_type" => "GIA",
                                    "certification_number" => "1234567890",
                                    "photo_link" => "https://cdn.com/item-image.png",
                                    "description_full" => "full description of the bracelet",
                                    "description_short" => "short description of the bracelet",
                                    "weight" => "",
                                    "purchase_price" => [
                                      "amount"=> 12567,
                                      "currency"=> "USD"
                                    ],
                                    "estimated_value" => [
                                      "amount" => 34589,
                                      "currency" => "USD"
                                    ],
                                    "serial_number" => "12345",
                                    "model_number" => "54321"
                                  ],
                                  [
                                    "quantity"=> 1,
                                    "type"=> "bracelet",
                                    "sku" => "235s9f94",
                                    "certification_type" => "GIA",
                                    "certification_number" => "1232227890",
                                    "photo_link" => "http://image.com/item.png",
                                    "description_full" => "full description of the bracelet",
                                    "description_short" => "short description of the bracelet",
                                    "weight" => "",
                                    "purchase_price" => [
                                      "amount"=> 56825,
                                      "currency"=> "USD"
                                    ],
                                    "estimated_value" => [
                                      "amount" => 60825,
                                      "currency" => "USD"
                                    ],
                                    "serial_number" => "16845",
                                    "model_number" => "53551"
                                  ]
                              ]
                          ]
                      ]
                   ]
                ];
                break;
            case "bad-post-order-empty-items":
                $result = [
                    "order" => [
                      "order_number" => $this->orderID,
                      "binder_requested" => true,
                      "customer" => [
                          "email" => "dwight+swagger-test@myzillion.com",
                          "first_name" => "Dwight",
                          "last_name" => "Schrute",
                          "mobile_phone" => "123-123-1234",
                          "billing_city" => "Scranton",
                          "billing_street" => "1725 Slough Avenue",
                          "billing_city" => "California",
                          "billing_zip" => "98765",
                      ],
                      "item_groups" => [
                          [
                              "description" => "description of the grouped / finished item",
                              "name" => "name of the grouped / finished item",
                              "photo_link" => "https://cdn.com/finished-item-image.png",
                              "items"=> []
                          ]
                      ]
                   ]
                ];
                break;
        }
        return $result;
    }
}
