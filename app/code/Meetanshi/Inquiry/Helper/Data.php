<?php

namespace Meetanshi\Inquiry\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use Zend\Log\Writer\Stream;
use Zend\Log\Logger;

class Data extends AbstractHelper
{
    public function getDealerInquiryEnable()
    {
        return $this->scopeConfig->getValue(
            'dealer_inquiry/settings/enable',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getMetaTitle()
    {
        return $this->scopeConfig->getValue(
            'dealer_inquiry/settings/meta_title',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getMetaDescription()
    {
        return $this->scopeConfig->getValue(
            'dealer_inquiry/settings/meta_description',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getUrlKey()
    {
        return $this->scopeConfig->getValue(
            'dealer_inquiry/settings/url_key',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getTopLinkEnable()
    {
        return $this->scopeConfig->getValue(
            'dealer_inquiry/settings/enable_top_link',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getFooterLinkEnable()
    {
        return $this->scopeConfig->getValue(
            'dealer_inquiry/settings/enable_footer_link',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getCustomerGroup()
    {
        return $this->scopeConfig->getValue(
            'dealer_inquiry/settings/customer_group',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getHeading()
    {
        return $this->scopeConfig->getValue(
            'dealer_inquiry/form/form_settings/heading',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getDescription()
    {
        return $this->scopeConfig->getValue(
            'dealer_inquiry/form/form_settings/description',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getSubmitButtonLabel()
    {
        return $this->scopeConfig->getValue(
            'dealer_inquiry/form/form_settings/submit_button_label',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getSuccessMessage()
    {
        return $this->scopeConfig->getValue(
            'dealer_inquiry/form/form_settings/success_message',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getSelectedFields()
    {
        return $this->scopeConfig->getValue(
            'dealer_inquiry/form/form_settings/enable_fields',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getAllowedFileTypes()
    {
        return $this->scopeConfig->getValue(
            'dealer_inquiry/form/form_settings/allowed_file_types',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getFirstName()
    {
        return $this->scopeConfig->getValue(
            'dealer_inquiry/form/change_label/first_name',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getLastName()
    {
        return $this->scopeConfig->getValue(
            'dealer_inquiry/form/change_label/last_name',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getCompanyName()
    {
        return $this->scopeConfig->getValue(
            'dealer_inquiry/form/change_label/company_name',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getTaxVatNumber()
    {
        return $this->scopeConfig->getValue(
            'dealer_inquiry/form/change_label/tax_vat_number',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getStreetAddress()
    {
        return $this->scopeConfig->getValue(
            'dealer_inquiry/form/change_label/street_address',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getCity()
    {
        return $this->scopeConfig->getValue(
            'dealer_inquiry/form/change_label/city',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getState()
    {
        return $this->scopeConfig->getValue(
            'dealer_inquiry/form/change_label/state',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getCountry()
    {
        return $this->scopeConfig->getValue(
            'dealer_inquiry/form/change_label/country',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getZipPostalCode()
    {
        return $this->scopeConfig->getValue(
            'dealer_inquiry/form/change_label/zip_postal_code',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getContactNumber()
    {
        return $this->scopeConfig->getValue(
            'dealer_inquiry/form/change_label/contact_number',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getEmail()
    {
        return $this->scopeConfig->getValue(
            'dealer_inquiry/form/change_label/email',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getWebsite()
    {
        return $this->scopeConfig->getValue(
            'dealer_inquiry/form/change_label/website',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getBusinessDescription()
    {
        return $this->scopeConfig->getValue(
            'dealer_inquiry/form/change_label/description',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getDateTime()
    {
        return $this->scopeConfig->getValue(
            'dealer_inquiry/form/change_label/date_time',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getUploadFiles()
    {
        return $this->scopeConfig->getValue(
            'dealer_inquiry/form/change_label/upload_files',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getExtraField1()
    {
        return $this->scopeConfig->getValue(
            'dealer_inquiry/form/change_label/extra_field_1',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getExtraField2()
    {
        return $this->scopeConfig->getValue(
            'dealer_inquiry/form/change_label/extra_field_2',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getExtraField3()
    {
        return $this->scopeConfig->getValue(
            'dealer_inquiry/form/change_label/extra_field_3',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getOwnerEmailTemplate()
    {
        return $this->scopeConfig->getValue(
            'dealer_inquiry/owner_template/owner_email_template',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getOwnerEmail()
    {
        return $this->scopeConfig->getValue(
            'dealer_inquiry/owner_template/owner_email',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getCustomerEmailSender()
    {
        return $this->scopeConfig->getValue(
            'dealer_inquiry/customer_template/customer_email_sender',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getCustomerEmailTemplate()
    {
        return $this->scopeConfig->getValue(
            'dealer_inquiry/customer_template/customer_email_template',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getCreateCustomerEmailSender()
    {
        return $this->scopeConfig->getValue(
            'dealer_inquiry/create_customer_template/create_customer_email_sender',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getCreateCustomerEmailTemplate()
    {
        return $this->scopeConfig->getValue(
            'dealer_inquiry/create_customer_template/create_customer_email_template',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getEnableTerm()
    {
        return $this->scopeConfig->getValue(
            'dealer_inquiry/term_condition/enable_fields',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getTermContent()
    {
        return $this->scopeConfig->getValue(
            'dealer_inquiry/term_condition/content',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getRecaptchaEnable()
    {
        return $this->scopeConfig->getValue(
            'dealer_inquiry/recaptcha/enable_fields',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getSiteKey()
    {
        return $this->scopeConfig->getValue(
            'dealer_inquiry/recaptcha/site_key',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getSecretKey()
    {
        return $this->scopeConfig->getValue(
            'dealer_inquiry/recaptcha/secret_key',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getSenderEmailAddress($code)
    {
        return $this->scopeConfig->getValue(
            'trans_email/ident_' . $code . '/email',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getSenderEmailName($code)
    {
        return $this->scopeConfig->getValue(
            'trans_email/ident_' . $code . '/name',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function printLog($log)
    {
        $writer = new Stream(BP . '/var/log/inquiry.log');
        $logger = new Logger();
        $logger->addWriter($writer);
        $logger->info($log);
    }

    public function validate($token)
    {
        $verification = [
            'success' => false,
            'error' => 'The request is invalid or malformed.'
        ];
        if ($token) {
            try {
                $secret = $this->getSecretKey();
                $url = 'https://www.google.com/recaptcha/api/siteverify?secret=' .
                    $secret . '&response=' . $token . '&remoteip=' . $_SERVER["REMOTE_ADDR"];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $response = curl_exec($ch);

                $result = json_decode($response, true);

                if ($result['success']) {
                    $verification['success'] = true;
                    $verification['error'] = '';
                } elseif (array_key_exists('error-codes', $result)) {
                    $verification['error'] = $this->getErrorMessage($result['error-codes'][0]);
                }
            } catch (\Exception $e) {
                $verification['error'] = __($e->getMessage());
            }
        }
        return $verification;
    }

    private function getErrorMessage($errorCode)
    {
        $errorCodesGoogle = [
            'missing-input-secret' => __('The secret parameter is missing.'),
            'invalid-input-secret' => __('The secret parameter is invalid or malformed.'),
            'missing-input-response' => __('The response parameter is missing.'),
            'invalid-input-response' => __('The response parameter is invalid or malformed.'),
            'bad-request' => __('The request is invalid or malformed.')
        ];

        if (array_key_exists($errorCode, $errorCodesGoogle)) {
            return $errorCodesGoogle[$errorCode];
        }
        return __('Something is wrong.');
    }
}
