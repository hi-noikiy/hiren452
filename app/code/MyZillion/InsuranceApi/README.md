MyZillion API Wrapper
=====================

Installation
------------
### Using composer
 * ``` $ composer require myzillion/insurance-api```

Requirements
------------

### Production
 * Php `>=5.6`
 * Curl

### Development
 * PHPUnit

Note: *Please, keep in mind having the right relation between the PHP and PHPUnit versions*

Usage
-----
Make an array with your credentials to make a Client Object later, structure should be like:
 * `$credentials = array( "api_key" => "MyAPIKey")`

Instantiate an object of the wrapper to use it:
 * `$client = new \MyZillion\InsuranceApi\Client( array $credentials, boolean $useProductionApi )`

The wrapper has two public functions for purpose use:
 * `getOffer( array $data )`
 * `postOrder( array $data )`

When `$data` is an array with the information required by the API to process the request.

Testing
-------
 * Set up test credentials using the environment variables
   * API_KEY: `MYZILLION_API_KEY`
 * Go to module folder and run:

    `$ phpunit`

Note: *All the tests are currently set up to check the wrapper against the staging endpoint*

### Tests Details
Seven tests were implemented to check the right behavior of the wrapper
  1. [x] **testBadCredentials**
     * *Validates response of the wrapper with bad credentials (401 expected)*
  2. [x] **testGetQuoteWithProperData**
     * *Validates the response has the expected format using good information*
  3. [x] **testGetOfferWithWrongZipCode**
    * *Validates the wrapper returns a certain list of errors*
  4. [x] **testGetQuoteWithBadData**
     * *Validates the wrapper returns a certain list of errors*
  5. [x] **testGetQuoteWithEmptyItemsData**
     * *Validates the wrapper returns a certain list of errors*
  6. [x] **testOrderPostWithProperData**
     * *Validates the response has the expected format using good information*
  7. [x] **testOrderPostWithBadData**
     * *Validates the wrapper returns a certain list of errors*

___
License
-------
**No license so far**
