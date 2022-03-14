<?php
/**
 * Copyright (c) 2020, Zillion Insurance Services, Inc.
 * All rights reserved.
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *   * Redistributions of source code must retain the above copyright notice,
 *     this list of conditions and the following disclaimer.
 *   * Redistributions in binary form must reproduce the above copyright notice,
 *     this list of conditions and the following disclaimer in the documentation
 *     and/or other materials provided with the distribution.
 *   * Neither the name of Zend Technologies USA, Inc. nor the names of its
 *     contributors may be used to endorse or promote products derived from this
 *     software without specific prior written permission.
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @package myzillion
 * @subpackage module-simplified-insurance
 * @author Serfe <info@serfe.com>
 * @copyright 2020 Zillion Insurance Services, Inc.
 * @since 1.0.0
 */

namespace MyZillion\SimplifiedInsurance\Model;

use Magento\Catalog\Model\Product;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\ShipmentInterface;
use MyZillion\SimplifiedInsurance\Api\MapperInterface;
use MyZillion\SimplifiedInsurance\Helper\Data;
use MyZillion\SimplifiedInsurance\Model\Config\Source\TypeSourceAttributes;

/**
 * Transforms Magento data into arrays with the required structure to request the Zillion API
 */
class Mapper implements MapperInterface
{

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    private $serializer;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var \Magento\Catalog\Model\CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var  @var \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSet
     */
    protected $attributeSet;

    /**
     * @var \MyZillion\SimplifiedInsurance\Helper\ConfigHelper
     */
    private $configHelper;

    /**
     * @var \Magento\Framework\Escaper
     */
    private $escaper;

    /**
     * @var \Magento\Bundle\Api\ProductLinkManagementInterface
     */
    private $productLinkManagementInterface;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \MyZillion\SimplifiedInsurance\Helper\Data
     */
    private $zillionHelper;

    /**
     * Constructor
     *
     * @param \Magento\Quote\Api\CartRepositoryInterface         $quoteRepository
     * @param \Magento\Framework\Serialize\SerializerInterface   $serializer
     * @param \Magento\Catalog\Api\ProductRepositoryInterface    $productRepository
     * @param \Magento\Catalog\Model\CategoryRepository          $categoryRepository
     * @param \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSet
     * @param \MyZillion\SimplifiedInsurance\Helper\ConfigHelper $configHelper
     * @param \Magento\Framework\Escaper                        $escaper
     * @param \Magento\Bundle\Api\ProductLinkManagementInterface $productLinkManagementInterface
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \MyZillion\SimplifiedInsurance\Helper\Data $zillionHelper
     */
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSet,
        \MyZillion\SimplifiedInsurance\Helper\ConfigHelper $configHelper,
        \Magento\Framework\Escaper $escaper,
        \Magento\Bundle\Api\ProductLinkManagementInterface $productLinkManagementInterface,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \MyZillion\SimplifiedInsurance\Helper\Data $zillionHelper
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->serializer = $serializer;
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->attributeSet = $attributeSet;
        $this->configHelper = $configHelper;
        $this->escaper = $escaper;
        $this->productLinkManagementInterface = $productLinkManagementInterface;
        $this->storeManager = $storeManager;
        $this->zillionHelper = $zillionHelper;
    }

    /**
     * Generates an array with the offer structure from a quote object
     * Return value https://gist.github.com/zillion-integrations/8e3a7d2e4328554dff6eb2defa308743#data-payload
     *
     * @param string $postCodeFrontEnd
     * @param CartInterface $quote
     * @return array
     */
    public function quoteToOffer(CartInterface $quote, string $postCodeFrontEnd)
    {
        $offer = [];
        // Preparation of auxiliaries
        $address = $quote->getShippingAddress();

        $offer['order_id'] = $quote->getId();

        // If the postal code of the quote is null, the one of frontendjs is used
        $offer['zip_code'] = ($address->getPostcode() ? $address->getPostcode() : $postCodeFrontEnd);

        // Add module version to the request data payload
        $version = $this->zillionHelper->getModuleVersionFromComposer();
        if ($version) {
            $offer['module_version'] = $version;
        }

        // Preparation for building offer items
        $items = [];
        $itemsCollection = $quote->getItems();
        if ($itemsCollection) {
            foreach ($quote->getItems() as $quoteItem) {
                $product = $quoteItem->getProduct();
                // Configurable check product grouper
                $typeProduct = $product->getTypeId();
                if ($typeProduct === 'simple' || $typeProduct === 'configurable') {
                    // The original product price is used as the insurance requires the original price
                    // without any applicable discounts
                    $qty = (int) $quoteItem->getQty();
                    $price = (string) $quoteItem->getPrice();
                    $productType = $this->getProductType($product);
                    
                    $item = [
                        'value'    => number_format($price, 2),
                        'type'     => $productType,
                        'quantity' => (int) $qty,
                    ];
                    $items[] = $item;
                } elseif ($typeProduct === 'bundle') {
                    $productChildrenItems = $quoteItem->getChildren();
                    if ($productChildrenItems) {
                        foreach ($productChildrenItems as $productChildItem) {
                            $productChild = $productChildItem->getProduct();
                            // The original product price is used as the insurance requires the original price
                            // without any applicable discounts
                            $qty = (int) $productChildItem->getQty();
                            $price = (string) $productChildItem->getPrice();
                            $productType = $this->getProductType($productChild);
                            
                            $item = [
                                'value'    => number_format($price, 2),
                                'type'     => $productType,
                                'quantity' => (int) $qty,
                            ];
                            $items[] = $item;
                        }
                    }
                }
            }
        }

        $offer['items'] = $items;

        return $offer;
    }

    /**
     * Generates an array with the structure from a order object
     * Return value https://gist.github.com/zillion-integrations/09545fd84c307d30992c4ecaf351b835#data-payload
     *
     * @param ShipmentInterface $shipment
     * @return array
     */
    public function shipmentToPostOrderRequest(ShipmentInterface $shipment)
    {
        $postOrderRequest = [];
        // Add module version to the request data payload
        $version = $this->zillionHelper->getModuleVersionFromComposer();
        if ($version) {
            $postOrderRequest['module_version'] = $version;
        }

        // Preparation of auxiliaries
        $order = $shipment->getOrder();
        // Retrieve current website id
        $storeId = $order->getStoreId();
        $websiteId = $this->storeManager->getStore($storeId)->getWebsiteId();

        $this->orderCurrency = $order->getOrderCurrencyCode();
        $quoteId = $order->getQuoteId();
        $quote = $this->quoteRepository->get($quoteId);
        $customer = $this->extractCustomerData($order, $quote);
        $itemsGroups = $this->generateItemsGroups($shipment);

        $binderRequested = ($quote->getData(Data::CUSTOMER_REQUEST_INSURANCE) == 1) ? true : false;
        $postOrderRequest['order']['order_number'] = $order->getRealOrderId();

        $requestedParam = 'binder_requested';
        $settingType = $this->configHelper->getZillionOfferType($websiteId);
        if ($settingType == 'quote') {
            $requestedParam = 'quote_requested';
        }

        $postOrderRequest['order'][$requestedParam] = $binderRequested;
        $postOrderRequest['order']['customer'] = $customer;
        $postOrderRequest['order']['item_groups'] = $itemsGroups;

        return $postOrderRequest;
    }

    /**
     * Generate group items
     *
     * @param ShipmentInterface $shipment
     * @return array
     */
    private function generateItemsGroups(ShipmentInterface $shipment)
    {
        $itemsGroups = [];
        $shipmentItems = $shipment->getAllItems();
        foreach ($shipmentItems as $shipmentItem) {
            $itemsGroups[] = $this->getDataByProductType($shipmentItem);
        }

        return $itemsGroups;
    }

    /**
     * Check product type for Simple, Configurable,
     * Grouped more inf https://docs.magento.com/user-guide/catalog/product-types.html
     *
     * @param ShipmentInterface[] $item
     * @return array
     */
    private function getDataByProductType($item)
    {
        $returnItems = [];
        $websiteId = null;
        try {
            $storeId = $item->getStoreId();
            $websiteId = $this->storeManager->getStore($storeId)->getWebsiteId();
            $product = $this->productRepository->getById($item->getProductId(), false, $storeId);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $ex) {
            return $returnItems;
        }

        // Preparation of auxiliaries
        $imageLink = $product->getMediaConfig()->getMediaUrl($product->getImage());

        // Load data general
        $returnItems['description'] = $this->getProductDescription($product, $websiteId);
        $returnItems['name'] = $product->getName();
        $returnItems['photo_link'] = $imageLink;
        // Configurable check product grouper
        $typeProduct = $product->getTypeId();
        if ($typeProduct === 'simple' || $typeProduct === 'configurable') {
            $orderItem = $item->getOrderItem();
            $productPrice = $orderItem->getPrice();
            $zillionType = $this->getProductType($product);
            if ($typeProduct === 'configurable') {
                // load simple product data
                $productOptions = $orderItem->getProductOptions();
                if (isset($productOptions['simple_sku'])) {
                    $product = $this->productRepository->get($productOptions['simple_sku']);
                }
            }

            $itemx = [];

            // Preparation of auxiliaries
            $imageLink = $product->getMediaConfig()->getMediaUrl($product->getImage());

            // Load item data for item
            $itemx['sku'] = $product->getSku();
            $itemx['type'] = $zillionType;
            $itemx['quantity'] = (int) $item->getQty();
            $itemx['photo_link'] = $imageLink;
            $itemx['description_full'] = $this->getProductFullDescription($product, $websiteId);
            $itemx['description_short'] = $this->getProductShortDescription($product, $websiteId);
            $itemx['weight'] = $product->getWeight() ?: (string) $product->getWeight();
            // Order item price
            $itemx['purchase_price']['amount'] = $this->convertPriceFormat($productPrice);
            $itemx['purchase_price']['currency'] = $this->orderCurrency;
            // Product base price
            $itemx['estimated_value']['amount'] = $this->convertPriceFormat($product->getPrice());
            $itemx['estimated_value']['currency'] = $this->orderCurrency;

            // Add extra data
            $extraData = $this->getProductExtraMappedAttributesValues($product, $websiteId);
            foreach ($extraData as $code => $value) {
                $itemx[$code] = $value;
            }

            $returnItems['items'][] = $itemx;
        } elseif ($typeProduct === 'bundle') {
            $orderItemData = $item->getOrderItem();
            $orderItems = $orderItemData->getChildrenItems();
            $parentZillionType = $this->getProductType($product);

            foreach ($orderItems as $orderItem) {
                $itemx = [];

                $productId = $orderItem->getProductId();
                $productPrice = $orderItem->getPrice();
                // load date product
                $simpleProduct = $this->productRepository->getById($productId);
                // Preparation of auxiliaries
                $imageLink = $simpleProduct->getMediaConfig()->getMediaUrl($simpleProduct->getImage());

                // Load item data for item
                $itemx['type'] = $this->getProductType($simpleProduct) ?: $parentZillionType;
                $itemx['quantity'] = (int) $orderItem->getQtyOrdered();
                $itemx['sku'] = $simpleProduct->getSku();
                $itemx['photo_link'] = $imageLink;
                $itemx['description_full'] = $this->getProductFullDescription($simpleProduct, $websiteId);
                $itemx['description_short'] = $this->getProductShortDescription($simpleProduct, $websiteId);
                $itemx['weight'] = $simpleProduct->getWeight();
                // Order item price
                $itemx['purchase_price']['amount'] = $this->convertPriceFormat($productPrice);
                $itemx['purchase_price']['currency'] = $this->orderCurrency;
                // Product base price
                $itemx['estimated_value']['amount'] = $this->convertPriceFormat($simpleProduct->getPrice());
                $itemx['estimated_value']['currency'] = $this->orderCurrency;

                // Add extra data
                $extraData = $this->getProductExtraMappedAttributesValues($product, $websiteId);
                foreach ($extraData as $code => $value) {
                    $itemx[$code] = $value;
                }

                $returnItems['items'][] = $itemx;
            }
        }

        return $returnItems;
    }

    /**
     * Converts the price value to the required format
     * amount: Required is passing estimated value. # of cents. i.e. $12,345.67 should be sent as 1234567. (Int)
     *
     * @param float $price
     * @return integer
     */
    private function convertPriceFormat($price)
    {
        return (integer) number_format($price, 2, '', '');
    }

    /**
     * Extract customer data
     *
     * @param OrderInterface $order
     * @param CartInterface|null $quote
     * @return array
     */
    private function extractCustomerData(OrderInterface $order, $quote)
    {
        $customer = [];

        // Preparation of auxiliaries
        $billingAddress = $order->getBillingAddress();
        $shippingAddress = $order->getShippingAddress();

        // Load data
        $customer['email'] = $order->getCustomerEmail();
        $customer['first_name'] = $order->getCustomerFirstname() ?: $billingAddress->getFirstname();
        $customer['last_name'] = $order->getCustomerLastname() ?: $billingAddress->getLastname();
        $customer['mobile_phone'] = $billingAddress->getTelephone();
        // As of #81756 updated the billing_address to shipping address temporarily
        // This change shouldn't be permanent
        $customer['billing_street'] = $shippingAddress->getStreet()[0];
        $customer['billing_city']   = $shippingAddress->getCity();
        $customer['billing_state']  = $shippingAddress->getRegion();
        $customer['billing_zip']    = $shippingAddress->getPostcode();

        return $customer;
    }

    /**
     * Get Street lines
     *
     * @param \Magento\Sales\Api\Data\OrderAddressInterface $address
     * @param string $baseIndex
     * @return array
     */
    private function getStreetLines($address, $baseIndex)
    {
        $streetLines = $address->getStreet();
        $customerLines = [];
        $i = 1;
        if (is_array($streetLines)) {
            foreach ($streetLines as $streetLine) {
                $index = $baseIndex . $i;
                $customerLines[$index] = $streetLine;
                $i++;
            }
        } elseif (is_string($streetLines)) {
            $index = $baseIndex . $i;
            $customerLines[$index] = $streetLines;
        }

        return $customerLines;
    }

    /**
     * Get Offer Id returned from the API before the purchase
     *
     * @param CartInterface|null $quote
     * @return string
     */
    protected function getOfferId($quote)
    {
        $offerId = '';
        if ($quote) {
            $offerId = $this->extractOfferDataFromQuote($quote, 'zillion_customer_anonymous_id');
        }

        return $offerId;
    }

    /**
     * Parse the data from the Offer API response and return its value
     *
     * @param CartInterface $quote
     * @param string $dataIndex
     * @return string
     */
    private function extractOfferDataFromQuote(CartInterface $quote, $dataIndex)
    {
        $offerResponseStr = $quote->getData(Data::OFFER_RESPONSE);
        $offerResponse = $this->serializer->unserialize($offerResponseStr);
        if (isset($offerResponse['offer']) && isset($offerResponse['offer'][$dataIndex])) {
            $offerId = $offerResponse['offer'][$dataIndex];
        }

        return $offerId;
    }

    /**
     * Get Zillion Product Type
     *
     * @param Product $product
     * @return string|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getProductType(Product $product)
    {
        $sourceType = $this->configHelper->getProductTypeSource();
        if ($sourceType === TypeSourceAttributes::SOURCE_PRODUCT_ATTRIBUTE['value']) {
            $response = $this->getProductTypeAttribute($product);
        } elseif ($sourceType === TypeSourceAttributes::SOURCE_PRODUCT_ATTRIBUTE_SET['value']) {
            $response = $this->getProductTypeAttributeSetName($product);
        } else {
            $msg = sprintf(
                __("Zillion: Check your configuration, the indicated resource '%s' is not contemplated"),
                $sourceType
            );
            throw new \Magento\Framework\Exception\LocalizedException($msg);
        }

        return strtolower($response);
    }

    /**
     * Get Zillion Product Type Attribute Set
     *
     * @param Product $product
     * @return string|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getProductTypeAttributeSetName(Product $product)
    {
        $attributeSetRepository = $this->attributeSet->get($product->getAttributeSetId());
        $productType = $attributeSetRepository->getAttributeSetName();
        return $productType;
    }

    /**
     * Get Zillion Product Type
     *
     * @param Product $product
     * @return string|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getProductTypeAttribute(Product $product)
    {
        $attributeCode = $this->configHelper->getDefaultProductType();
        if (!$attributeCode || $attributeCode === 0) {
            $msg = 'Zillion Type is not properly configured';
            throw new \Magento\Framework\Exception\LocalizedException(__($msg));
        }

        $product->getAttributeText($attributeCode);
        $productType = $product->getAttributeText($attributeCode);

        if (empty($productType)) {
            // If attribute is not available then load product information
            $product = $product->load($product->getId());
            $productType = $product->getAttributeText($attributeCode);
        }

        if (empty($productType)) {
            $productType = $this->getProductAttribute($product, $attributeCode);
        }

        if (empty($productType)) {
            $productType = null;
        } else {
            $productType = (string) $productType;
        }

        return $productType;
    }

    /**
     * Cut a string and append the $repl string
     *
     * @param  string $string
     * @param  string $repl
     * @param  integer $limit
     * @return string
     */
    private function cutString($string, $repl, $limit)
    {
        if (strlen($string) > $limit) {
            return substr($string, 0, $limit) . $repl;
        } else {
            return $string;
        }
    }

    /**
     * Parse string into a single line description
     *
     * @param  string $text
     * @return string|null
     */
    private function parseStringToSingleLine(string $text)
    {
        $text = str_replace(["\n", "\r", '"'], '', $text);
        $text = strip_tags($text);
        $text = htmlentities($text);
        $text = $this->escaper->escapeHtml($text, true);
        return $text;
    }

    /**
     * Retrieve product description field value
     *
     * @param  Product  $product
     * @param  string|integer $websiteId
     * @return string
     */
    private function getProductDescription($product, $websiteId = 0)
    {

        $attributeCode = $this->configHelper->getZillionDescriptionAttribute($websiteId);
        // Default description attribute if is not set
        if (!$attributeCode) {
            $attributeCode = 'description';
        }

        $description = $this->getProductAttribute($product, $attributeCode);

        if (!empty($description)) {
            $description = $this->parseStringToSingleLine($description);
        }else{
            $description = $this->parseStringToSingleLine(
                $this->getProductDescriptionNotEmpty($product)
            );
        }

        return $description;
    }

    /**
     * Retrieve short description field
     *
     * @param  Product $product
     * @param  string|integer $websiteId
     * @return string
     */
    private function getProductShortDescription($product, $websiteId = 0)
    {
        $attributeCode = $this->configHelper->getZillionDescriptionShortAttribute($websiteId);
        // Default description attribute if is not set
        if (!$attributeCode) {
            $attributeCode = 'description';
        }

        $shortDescription = $this->getProductAttribute($product, $attributeCode);
        if (!empty($shortDescription)) {
            $shortDescription = $this->parseStringToSingleLine($shortDescription);
        } else {
            // In case attribute is empty use the product name to generate the request since is a required attribute
            $shortDescription = $product->getName();
        }

        // API Short description max characters is 255
        return $this->cutString($shortDescription, '...', 255);
    }

    /**
     * Retrieve full description field content
     *
     * @param  Product $product
     * @param  string|integer $websiteId
     * @return string
     */
    private function getProductFullDescription($product, $websiteId = 0)
    {

        $attributeCode = $this->configHelper->getZillionDescriptionFullAttribute($websiteId);
        // Default description attribute if is not set
        if (!$attributeCode) {
            $attributeCode = 'description';
        }

        $fullDescription = $this->getProductAttribute($product, $attributeCode);
        if (!empty($fullDescription)) {
            $fullDescription = $this->parseStringToSingleLine($fullDescription);
        }else{
            $fullDescription = $this->parseStringToSingleLine(
                $this->getProductDescriptionNotEmpty($product)
            );
        }

        return $fullDescription;
    }

    /**
     * Retrieve full description field content
     *
     * @param  Product $product
     * @return string
     */
    private function getProductDescriptionNotEmpty($product){
        $alternativeDescription = $this->getProductAttribute($product, 'short_description');
        if(empty($alternativeDescription)){
            $alternativeDescription = sprintf("%s %s", $product->getSku(), $product->getName());
        }

        return $alternativeDescription;
    }

    /**
     * Collect attributes values from settings for:
     *
     *   - certification_type
     *   - certification_number
     *   - serial_number
     *   - model_number
     *
     * And return an array with them values
     *
     * @param  Product $product
     * @param  string|integer $websiteId
     * @return array
     */
    private function getProductExtraMappedAttributesValues($product, $websiteId)
    {
        $data = [];
        $codes = [];
        // Collect extra data if attribute is set
        $attributeCode = $this->configHelper->getZillionCertificationTypeAttribute($websiteId);
        if (!empty($attributeCode)) {
            $codes['certification_type'] = $attributeCode;
        }

        $attributeCode = $this->configHelper->getZillionCertificationNumberAttribute($websiteId);
        if (!empty($attributeCode)) {
            $codes['certification_number'] = $attributeCode;
        }

        $attributeCode = $this->configHelper->getZillionSerialNumberAttribute($websiteId);
        if (!empty($attributeCode)) {
            $codes['serial_number'] = $attributeCode;
        }

        $attributeCode = $this->configHelper->getZillionModelNumberAttribute($websiteId);
        if (!empty($attributeCode)) {
            $codes['model_number'] = $attributeCode;
        }

        foreach ($codes as $code => $attributeCode) {
            $value = $this->getProductAttribute($product, $attributeCode);
            if (!empty($value)) {
                $data[$code] = $value;
            }
        }

        return $data;
    }

    /**
     * Retrieve product attribute frontend value
     *
     * @param  Product $product
     * @param  string $attributeCode
     * @return string|integer|null
     */
    private function getProductAttribute($product, string $attributeCode)
    {
        $value = $product->getResource()->getAttribute($attributeCode)->getFrontend()->getValue($product);
        if (empty($value)) {
            $value = $product->getData($attributeCode);
        }

        return $value;
    }
}
