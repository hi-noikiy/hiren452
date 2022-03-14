# Reviews
Here we will find information about the interaction with the magento core or any other complementary information which is useful for the developer

## Developer notes

### Settings
Keep in mind that it is required to authenticate the API, access credentials which are requested from the Zillion team.

Available settings are:

* Enabled: Enable/disable module
* API Key: API key provided by Zillion Team.
* Test Mode: Enable/disable test mode
* Test Credentials Button: Button to check credentials. A offer request is sent into the API to check if the credentials are valid.
* Zillion Product Type Attribute: Define which attribute use to get the product Zillion type sent to Zillion API in the requests.
* Debug: Enable/Disable custom logger. Logs are saved into var/log/zillion-requests.log
* Zillion Box Type: Define the type of zillion box. Available options are:
  * Binder: The box display the amount of the offer request.
  * Quote: The box doesn't display the amount of the offer request

### Controllers

#### MyZillion\SimplifiedInsurance\Controller\Ajax\Offer

Frontend endpoint that collect current cart information and sent the Post Offer request to the Zillion API

#### MyZillion\SimplifiedInsurance\Controller\Adminhtml\TestCredentials\Index

Adminhtml controller that perform the validation of the credentials. It sents a post offer request with dummy data to validate the credentials.

#### MyZillion\SimplifiedInsurance\Controller\Adminhtml\PostOrder

Adminhtml controller that perform the retry action. It collects a shipment information and perform the Post Order action.

### Mapper
The mapper class is in charge of building the array so that it is consulted to the Zillion api
According to the detailed documentation below

* POST Offer:
    * Documentation: https://gist.github.com/zillion-integrations/0367e37aa773fa8fe62d0a80cb65b07b
    * Endpoint: https://sandbox.api.myzillion.com/ecommerce/v1/offer
* POST Order:
    *  Documentation: https://gist.github.com/zillion-integrations/9d54c6998f11cc59d2462ebca97327b5
    *  Endpoint: https://sandbox.api.myzillion.com/ecommerce/v1/order/
    *  Data Payload

Based on the configuration of the system:

* If the box template is set as "Binder", the offer request data payload will send the: "binder_requested" parameter
* If the box template is set as "Quote", the offer request data payload will send the: "quote_requested" parameter.

The parameter will sent true if the customer accepted the offer.

Both request also sent the **module_version** field with obtains the current module version from the composer.json file.

### Observer

#### MyZillion\SimplifiedInsurance\Observer\Sales\OrderSaveBefore

This observer update the order data with the zillion api offer request data from the related quote.

#### MyZillion\SimplifiedInsurance\Observer\Sales\OrderShipmentSaveAfter

This observer execute the Post Order action when a shipment is created.

### Plugins

#### MyZillion\SimplifiedInsurance\Plugin\Magento\Checkout\Model\GuestPaymentInformationManagement

Plugin that process the API guest place order request in order to update the quote with the zillion information

#### MyZillion\SimplifiedInsurance\Plugin\Magento\Checkout\Model\PaymentInformationManagement

Plugin that process the API registered customer place order request in order to update the quote with the zillion information

#### MyZillion\SimplifiedInsurance\Plugin\Magento\Shipping\Block\Adminhtml\View

Adds the "Zillion Order Request" button to the shipping review view

### Zillion Status Entity

We added a new table and entity to save the shipment post order status when a shipment is created.

The model, resourceModel and collection are:

 * MyZillion\SimplifiedInsurance\Model\ShipmentPostRequest
 * MyZillion\SimplifiedInsurance\Model\ResourceModel\ShipmentPostRequest
 * MyZillion\SimplifiedInsurance\Model\ResourceModel\ShipmentPostRequest\Collection

All of the methods are inherited from the Magento model classes

The new table 'zillion_shipment_post_request' schema is defined in: Setup/UpgradeSchema.php

### Integration Knockout Components

The integration with the checkout is handled via knockout component. The integration supports:

- Magento default checkout
- Amazon Pay checkout
- PayPal checkout

#### MyZillion_SimplifiedInsurance/js/form/element/insurance-checkbox

Box component that handle the display of the box into the default Magento checkout and the Amazon Pay checkout.

#### MyZillion_SimplifiedInsurance/js/form/element/insurance-checkbox-no-checkout

Box component that handle the display of the box into the PayPal review layouts.

It is initializated in a custom template: view/frontend/templates/paypal/box-container.phtml

#### MyZillion_SimplifiedInsurance/js/model/zillion

Model that handle the offer request process

#### MyZillion_SimplifiedInsurance/js/model/zillion-config

Model that handle the module configurations. Provide method to get the proper template to be displayed

#### MyZillion_SimplifiedInsurance/js/action/request-insurance

Action model that sent the request to the request-zillion-insurance endpoint used by zillion box checkout component. This update the selected value into the related quote.

#### MyZillion_SimplifiedInsurance/js/action/request-insurance-no-checkout

Action model that sent the request to the request-zillion-insurance endpoint used by zillion box  no checkout component. This update the selected value into the related quote.

#### Amazon Pay Mixin

Defined in view/frontend/web/js/mixins/amazon-payment/checkout-widget-address.js

This mixin add the call of the zillion.checkQuoteOffer() method after the Amazon Pay address is set.
