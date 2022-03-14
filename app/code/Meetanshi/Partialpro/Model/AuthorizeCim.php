<?php

namespace Meetanshi\Partialpro\Model;

use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;
use Magento\Framework\App\Config\ScopeConfigInterface;

class AuthorizeCim
{
    public function __construct(
        ScopeConfigInterface $scopeConfig
    )
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function createCustomerProfile($data, $quote)
    {
        $merchantId = $this->scopeConfig->getValue('payment/authorizenet_directpost/login');
        $merchantTransactionKey = $this->scopeConfig->getValue('payment/authorizenet_directpost/trans_key');
        $istestMode = $this->scopeConfig->getValue('payment/authorizenet_directpost/test');

        $billingAddress = $quote->getBillingAddress();
        $street = $billingAddress->getStreet();

        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName($merchantId);
        $merchantAuthentication->setTransactionKey($merchantTransactionKey);

        $refId = 'ref' . time();

        $creditCard = new AnetAPI\CreditCardType();
        $creditCard->setCardNumber($data['cc_number']);
        if (intval($data['cc_exp_month']) < 10) $data['cc_exp_month'] = '0' . $data['cc_exp_month'];
        $creditCard->setExpirationDate($data['cc_exp_year'] . '-' . $data['cc_exp_month']);
        if ($data['cc_id']) {
            $creditCard->setCardCode($data['cc_id']);
        }
        $paymentCreditCard = new AnetAPI\PaymentType();
        $paymentCreditCard->setCreditCard($creditCard);

        $billto = new AnetAPI\CustomerAddressType();
        $billto->setFirstName($billingAddress->getFirstname());
        $billto->setLastName($billingAddress->getLastname());
        $billto->setCompany($billingAddress->getCompany());
        $billto->setAddress($street[0]);
        $billto->setCity($billingAddress->getCity());
        $billto->setState($billingAddress->getRegion());
        $billto->setZip($billingAddress->getPostcode());
        $billto->setCountry($billingAddress->getCountryId());
        $billto->setPhoneNumber($billingAddress->getTelephone());
        $billto->setfaxNumber($billingAddress->getFax());

        $paymentprofile = new AnetAPI\CustomerPaymentProfileType();
        $paymentprofile->setCustomerType('individual');
        $paymentprofile->setBillTo($billto);
        $paymentprofile->setPayment($paymentCreditCard);
        $paymentprofile->setDefaultPaymentProfile(true);
        $paymentpros[] = $paymentprofile;

        $customerProfile = new AnetAPI\CustomerProfileType();
        $customerProfile->setMerchantCustomerId($quote->getId());
        $customerProfile->setEmail($quote->getCustomerEmail());
        $customerProfile->setPaymentProfiles($paymentpros);

        $request = new AnetAPI\CreateCustomerProfileRequest();
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setRefId($refId);
        $request->setProfile($customerProfile);

        $controller = new AnetController\CreateCustomerProfileController($request);
        if ($istestMode) {
            $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
        } else {
            $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);
        }
        if (($response != null) && ($response->getMessages()->getResultCode() == "Ok")) {
            $paymentProfiles = $response->getCustomerPaymentProfileIdList();
            $result['result'] = true;
            $result['customerProfileId'] = $response->getCustomerProfileId();
            $result['paymentProfileId'] = $paymentProfiles[0];
            return $result;

        } else {
            $errorMessages = $response->getMessages()->getMessage();

            if ($errorMessages[0]->getCode() == "E00039") {
                $existingcustomerprofileid = $this->getInbetweenStrings("ID ", " already", $errorMessages[0]->getText());

                $paymentprofilerequest = new AnetAPI\CreateCustomerPaymentProfileRequest();
                $paymentprofilerequest->setMerchantAuthentication($merchantAuthentication);

                $paymentprofilerequest->setCustomerProfileId($existingcustomerprofileid[0]);
                $paymentprofilerequest->setPaymentProfile($paymentprofile);
                if ($istestMode) {
                    $paymentprofilerequest->setValidationMode("testMode");
                }else{
                    $paymentprofilerequest->setValidationMode("liveMode");
                }


                $paymentController = new AnetController\CreateCustomerPaymentProfileController($paymentprofilerequest);

                if ($istestMode) {
                    $paymentResponse = $paymentController->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
                } else {
                    $paymentResponse = $paymentController->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);
                }
                if (($paymentResponse != null) && ($paymentResponse->getMessages()->getResultCode() == "Ok")) {

                    $result['result'] = true;
                    $result['customerProfileId'] = $existingcustomerprofileid[0];
                    $result['paymentProfileId'] = $paymentResponse->getCustomerPaymentProfileId();
                    return $result;
                } else {

                    $errorMessages = $paymentResponse->getMessages()->getMessage();
                    $emessage = "Error 1 on create profile of authorizenet : " . $errorMessages[0]->getCode() . "  " . $errorMessages[0]->getText() . "\n";

                    $result['result'] = false;
                    $result['message'] = $emessage;
                    return $result;
                }
            } else {

                $emessage = "Error 2 on create profile of authorizenet : " . $errorMessages[0]->getCode() . "  " . $errorMessages[0]->getText() . "\n";
                $result['result'] = false;
                $result['message'] = $emessage;
                return $result;
            }
        }
    }

    public function autoCaptureAuthorize($profileid, $paymentprofileid, $amount)
    {
        $merchantId = $this->scopeConfig->getValue('payment/authorizenet_directpost/login');
        $merchantTransactionKey = $this->scopeConfig->getValue('payment/authorizenet_directpost/trans_key');

        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName($merchantId);
        $merchantAuthentication->setTransactionKey($merchantTransactionKey);

        $refId = 'ref' . time();

        $profileToCharge = new AnetAPI\CustomerProfilePaymentType();
        $profileToCharge->setCustomerProfileId($profileid);
        $paymentProfile = new AnetAPI\PaymentProfileType();
        $paymentProfile->setPaymentProfileId($paymentprofileid);
        $profileToCharge->setPaymentProfile($paymentProfile);
        $transactionRequestType = new AnetAPI\TransactionRequestType();
        $transactionRequestType->setTransactionType("authCaptureTransaction");
        $transactionRequestType->setAmount($amount);
        $transactionRequestType->setProfile($profileToCharge);
        $request = new AnetAPI\CreateTransactionRequest();
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setRefId($refId);
        $request->setTransactionRequest($transactionRequestType);
        $controller = new AnetController\CreateTransactionController($request);
        if ($this->scopeConfig->getValue('payment/authorizenet_directpost/cgi_url') == "https://test.authorize.net/gateway/transact.dll") {
            $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
        } else {
            $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);
        }

        if ($response != null) {
            if ($response->getMessages()->getResultCode() == 'Ok') {
                $result["status"] = true;
                $result["trans_id"] = $response->getTransactionResponse()->getTransId();
                return $result;
            } else {
                $result["status"] = false;
                $errorMessages = $response->getMessages()->getMessage();
                $result["error_code"] = $errorMessages[0]->getCode();
                return $result;
            }

        }
        $result["status"] = false;
        $result["error_code"] = 0;
        return $result;
    }

    private function getInbetweenStrings($start, $end, $str)
    {
        $matches = [];
        $regex = "/$start([a-zA-Z0-9_]*)$end/";
        preg_match_all($regex, $str, $matches);
        return $matches[1];
    }
}