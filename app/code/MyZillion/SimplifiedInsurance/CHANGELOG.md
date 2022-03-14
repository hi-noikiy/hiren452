# MyZillion Simplified Insurance Module

## 1.0.15.4 - 2021-07-28
## Hotfix
- Fixed issue with shipment and error handling

## 1.0.15.3 - 2021-05-28
## Hotfix
- Fixed issue with amazon pay
- Fixed issue sending shippinf info to customer

## 1.0.15.2 - 2021-05-05
### Hotfix
- Fixed use case for `enable extension` and `unwanted offer`

## 1.0.15.1 - 2021-05-05
### Hotfix
- Fixed `strpos` code issue on `OrderSaveBefore.php`

## 1.0.15 - 2021-04-16
## Added
- Selenium automated tests and it's documentation #81995
## Updated
- Updated mechanism to send shipment to avoid duplicates #83747
- Updated zillion data sent when extension disabled #83481

## 1.0.14.1 - 2021-05-05
### Hotfix
- Fixed `strpos` code issue on `OrderSaveBefore.php`

## 1.0.14 - 2021-01-29
### Fixed
- Fixed offer for bundle products
- Fixed checkout offer showing for value-exceeded offers
### Updated
- Rollback `MyZillion_SimplifiedInsurance/js/action/request-offer` to 1.0.12
- Obtaining zip code from frontend in `MyZillion_SimplifiedInsurance/js/action/set-shipping-information`
- Updated mechanism to send the zip code from the frontend
- Updated billing information sent to get the info from the zip code #81756

## 1.0.13 - 2021-01-13
### Added
- Added asterisk for required settings
### Updated
- Updated logic for obtaining product description adding fallback mechanism to avoid blank
- Updated how zip code is obtained from the checkout process

## 1.0.12 - 2020-12-18
### Added
- Added setting to choose product type attribute source (product attributes, attribute sets)
### Fixed
- Fixed issue when saving old quotes/orders
- Fixed issue when shipping created and Api Key not configured
### Updated
- Updated logic to get product type from attribute set
- Updated templates for accessibility
- Updated configurations labels and locking SKU map

## 1.0.11 - 2020-10-13
### Updated
- Updated logic to get attribute values in data payload generation
- Updated logic to format displayed offer amount. Format number with decimal places
- Moved Zillion Product Type Map attribute map option into the attribute map settings section

## 1.0.10 - 2020-10-08
### Added
- Added module_version to the request data payload. Read current module version from the composer.json file
- Added settings to set description, short_description, full_description attributes
- Added settings to set certification_type, certification_number, serial_number, model_number attributes
- Added retry post order action button on shipment review view.
### Updated
- Updated Post Order data payload generation. Use new settings to get field values

## 1.0.9 - 2020-09-15
### Fixed
- Fixed UI styles
- Fixed templates misspelling issues
### Updated
- Updated checkout box links with UTM params
- Updated logic to always sent POST offer request into Zillion API

## 1.0.8 - 2020-09-14
### Added
- Updated store scope configuration logic
- Added support to configuration at website level. Multi website support
- Added new setting to allow change Zillion box template
- Update controllers and models to read new setting value
- Update Knockout.JS component logic to use new settings
### Fixed
- Updated LESS module styles. Fixed issue with Magento 2.2.x static content generation

## 1.0.7 - 2020-09-08
### Fixed
- Fixed issue with Amazon Pay new binder template. The binder value was not updated properly

## 1.0.6 - 2020-09-08
### Added
- Added new box templates for "binder box type" and "quote box type"
- Added new knockout component to handle box template configuration
### Modified
- Updated logic to load "binder box type" template.
- Updated logic to parse Zillion offer response and display offer value in zillion box

## 1.0.5 - 2020-08-31
### Added
- Added Zillion custom log file
- Added setting to enable/disable debug of request data sent and response
- Added support of store scope in settings helper
### Fixed
- Fixed issue with wrong constant identifier
- Removed debug code
- Fixed coding standard issues

## 1.0.4 - 2020-08-27
### Added
- Added button to validate credentials
- Added support to PayPal Payflow Express and PayPal Express no Magento default checkout
- Added support to Amazon Pay checkout method

## 1.0.3 - 2020-07-24
### Updated
- Updated zillion box styles

## 1.0.2 - 2020-07-23
### Updated
- Updated setting labels and description
- Updated box details and images sizes
- Updated confirmation modal text and buttons position

## 1.0.1 - 2020-07-20
### Fixed
- Fixed problem with shipment created via invoice create controller.
- Fixed problem with PayPal payments and quote.customer_request_insurance not set.
- Fixed issue with customer data section in Magento 2.2.x
- Fixed spelling issue in modal confirmation

## 1.0.0 - 2020-07-16
### Module creation and release
- Module base structure
- Added settings to allow module configuration
- Added dynamic setting to load available attributes
- Adding attributes for quote and order entities
- Incorporation of check and modal popup in checkout process. Create custom Knockout component
- Added endpoint (controller) to see if the products you select have an offer for insurance.
- Zillion Endpoint Data Formatting Aggregate (Mapper)
- Added plugin when saving the command, to capture CUSTOMER_REQUEST_INSURANCE
- Added observer, to listen every time an order is sent
- Added display of Zillion insurance request status in the admin order view
- Updated adapter and map methods to match with updated API data payload
- Added encryption of the api_key setting value
- Updated documentation
