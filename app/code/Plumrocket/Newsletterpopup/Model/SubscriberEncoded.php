<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_Newsletterpopup
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Newsletterpopup\Model;

use Magento\Customer\Model\AddressFactory as CustomerAddressFactory;
use Magento\Customer\Model\AttributeMetadataDataProvider;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Math\Random;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\SalesRule\Model\CouponFactory;
use Magento\SalesRule\Model\Coupon\Massgenerator;
use Magento\SalesRule\Model\Rule;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Plumrocket\Newsletterpopup\Helper\Adminhtml;
use Plumrocket\Newsletterpopup\Helper\Data;
use Plumrocket\Newsletterpopup\Model\Config\Source\Action;
use Plumrocket\Newsletterpopup\Model\Config\Source\MailchimpList as MailchimpListSource;
use Plumrocket\Newsletterpopup\Model\Config\Source\SignupMethod;
use Plumrocket\Newsletterpopup\Model\Config\Source\SubscriptionMode;
use Plumrocket\Newsletterpopup\Model\IntegrationRepositoryInterface;
use Plumrocket\Newsletterpopup\Model\ResourceModel\MailchimpList\CollectionFactory as ListCollectionFactory;
use Magento\Directory\Model\RegionFactory;

class SubscriberEncoded extends AbstractModel
{
    protected $_popup = null;
    protected $_fieldsForEmail = ['firstname', 'middlename', 'lastname'];
    protected $fieldsTag = null;

    protected $_session;
    protected $_storeManager;
    protected $_dataHelper;
    protected $_adminhtmlHelper;
    protected $_historyFactory;
    protected $_mailchimpListSource;
    protected $_couponFactory;
    protected $_massgenerator;
    protected $_scopeConfig;
    protected $_transportBuilder;
    protected $_inlineTranslation;
    protected $_resourceConnection;
    protected $_messageManager;
    protected $_customerFactory;
    protected $_customerAddressFactory;
    protected $_customerUrl;
    protected $_attributeMetadataDataProvider;
    protected $_directoryHelper;

    /**
     * @var IntegrationRepositoryInterface
     */
    private $integrationRepository;

    /**
     * @var ListCollectionFactory
     */
    private $listCollectionFactory;

    /**
     * @var RegionFactory
     */
    private $regionFactory;

    /**
     * SubscriberEncoded constructor.
     * @param Context $context
     * @param Registry $registry
     * @param Session $session
     * @param StoreManagerInterface $storeManager
     * @param Data $dataHelper
     * @param Adminhtml $adminhtmlHelper
     * @param HistoryFactory $historyFactory
     * @param MailchimpListSource $mailchimpListSource
     * @param Massgenerator $massgenerator
     * @param CouponFactory $couponFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param TransportBuilder $transportBuilder
     * @param StateInterface $inlineTranslation
     * @param ResourceConnection $resourceConnection
     * @param ManagerInterface $messageManager
     * @param CustomerFactory $customerFactory
     * @param CustomerAddressFactory $customerAddressFactory
     * @param CustomerUrl $customerUrl
     * @param AttributeMetadataDataProvider $attributeMetadataDataProvider
     * @param DirectoryHelper $directoryHelper
     * @param IntegrationRepositoryInterface $integrationRepository
     * @param ListCollectionFactory $listCollectionFactory
     * @param RegionFactory $regionFactory
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Session $session,
        StoreManagerInterface $storeManager,
        Data $dataHelper,
        Adminhtml $adminhtmlHelper,
        HistoryFactory $historyFactory,
        MailchimpListSource $mailchimpListSource,
        Massgenerator $massgenerator,
        CouponFactory $couponFactory,
        ScopeConfigInterface $scopeConfig,
        TransportBuilder $transportBuilder,
        StateInterface $inlineTranslation,
        ResourceConnection $resourceConnection,
        ManagerInterface $messageManager,
        CustomerFactory $customerFactory,
        CustomerAddressFactory $customerAddressFactory,
        CustomerUrl $customerUrl,
        AttributeMetadataDataProvider $attributeMetadataDataProvider,
        DirectoryHelper $directoryHelper,
        IntegrationRepositoryInterface $integrationRepository,
        ListCollectionFactory $listCollectionFactory,
        RegionFactory $regionFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_session = $session;
        $this->_storeManager = $storeManager;
        $this->_dataHelper = $dataHelper;
        $this->_adminhtmlHelper = $adminhtmlHelper;
        $this->_historyFactory = $historyFactory;
        $this->_mailchimpListSource = $mailchimpListSource;
        $this->_couponFactory = $couponFactory;
        $this->_massgenerator = $massgenerator;
        $this->_scopeConfig = $scopeConfig;
        $this->_transportBuilder = $transportBuilder;
        $this->_inlineTranslation = $inlineTranslation;
        $this->_resourceConnection = $resourceConnection;
        $this->_messageManager = $messageManager;
        $this->_customerFactory = $customerFactory;
        $this->_customerAddressFactory = $customerAddressFactory;
        $this->_customerUrl = $customerUrl;
        $this->_attributeMetadataDataProvider = $attributeMetadataDataProvider;
        $this->_directoryHelper = $directoryHelper;
        $this->integrationRepository = $integrationRepository;
        $this->listCollectionFactory = $listCollectionFactory;
        $this->regionFactory = $regionFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    public function subscribe($email, $data)
    {
        if ($this->_dataHelper->moduleEnabled() && $this->_getPopup()->getId() > 0) {
            // create coupon
            $couponCode = $this->_createCoupon();
            $data['coupon'] = $couponCode;

            // subscribe to integrations
            $this->subscribeToIntegrations($email, $data);

            // send email
            if ($this->_getPopup()->getSendEmail()) {
                $this->_sendEmail($email, $couponCode, $data);
            }

            // log subscription
            $this->_addHistory(Action::SUBSCRIBE, $email, $couponCode);
            return true;
        }
        return false;
    }

    public function getPopup()
    {
        return $this->_getPopup();
    }

    /**
     * @param $email
     * @param $postData
     * @return $this
     */
    private function subscribeToIntegrations($email, $postData)
    {
        if (! empty($email)) {
            /* MailChimp */
            $this->_subscribeToMailchimp($email, $postData);

            /** @var \Plumrocket\Newsletterpopup\Model\IntegrationInterface $integration */
            foreach ($this->integrationRepository->getList() as $integration) {
                if ($integration->isEnable()) {
                    $activeLists = $this->getActiveIntegrationLists($integration, $postData);

                    if (! empty($activeLists)) {
                        $integration->addContactToList($email, $activeLists, $postData);
                    }
                }
            }
        }

        return $this;
    }

    /**
     * @param \Plumrocket\Newsletterpopup\Model\IntegrationInterface $integration
     * @param $postData
     * @return array
     */
    private function getActiveIntegrationLists(
        \Plumrocket\Newsletterpopup\Model\IntegrationInterface $integration,
        $postData
    ) {
        $result = [];
        $integrationId = $integration->getIntegrationId();
        /** @var null|\Plumrocket\Newsletterpopup\Model\Popup $popup */
        $popup = $this->_getPopup();

        if ($popup
            && $popup->getId()
            && $popup->getPreparedIntegrationEnable($integrationId)
        ) {
            $mode = $popup->getPreparedIntegrationMode($integrationId);

            switch ($mode) {
                case SubscriptionMode::ALL_LIST:
                    $result = array_keys($integration->getAllLists());
                    break;
                case SubscriptionMode::ALL_SELECTED_LIST:
                    $result = $this->getSavedIntegrationLists($integrationId);
                    break;
                default:
                    if (! empty($postData['integration'][$integrationId])) {
                        $result = ! is_array($postData['integration'][$integrationId])
                            ? [(string)$postData['integration'][$integrationId]]
                            : $postData['integration'][$integrationId];
                    }
            }
        }

        return $result;
    }

    /**
     * @param $integrationId
     * @return array
     */
    private function getSavedIntegrationLists($integrationId)
    {
        /** @var \Plumrocket\Newsletterpopup\Model\ResourceModel\MailchimpList\Collection $collection */
        $collection = $this->listCollectionFactory->create();
        $collection->addIntegrationAndPopupFilter($integrationId, $this->_getPopup()->getId(), true);
        $collection->setLoadOnlyListIds();

        return $collection->getColumnValues('name');
    }

    protected function _subscribeToMailchimp($email, $data)
    {
        if ($this->_adminhtmlHelper->getMcapi()) {
            $mailchimpLists = $this->_mailchimpListSource->toOptionHash();
            $list = $this->_getActiveMailchimpList($data);
            if ($this->fieldsTag === null) {
                $this->fieldsTag = json_decode(
                    (string) $this->_dataHelper->getConfig(Data::SECTION_ID . '/integration/mailchimp/fields_tag')
                );
            }
            $mergeVars = [];
            foreach ($this->fieldsTag as $key => $tag) {
                if (isset($data[$key])) {
                    if ('dob' == mb_strtolower($tag)) {
                        $data[$key] = preg_replace('#\/\d{4}#', '', $data[$key]);
                    }

                    if ('gender' == mb_strtolower($tag)) {
                        $attribute = $this->_customerFactory->create()->getAttribute('gender');
                        $source = method_exists($attribute, 'getSource') ? $attribute->getSource() : null;

                        if ($source) {
                            $data[$key] = $source->getOptionText($data[$key]);
                        }
                    }

                    $mergeVars[$tag] = $data[$key];
                }
            }

            foreach ($list as $id) {
                if (array_key_exists($id, $mailchimpLists)) {
                    $this->_adminhtmlHelper->getMcapi()->listSubscribe(
                        $id,
                        $email,
                        $mergeVars,
                        'html',
                        (int)$this->_dataHelper->getConfig(Data::SECTION_ID . '/integration/mailchimp/send_email') === 1
                    );
                }
            }
        }
    }

    protected function _getActiveMailchimpList($data)
    {
        $list = [];
        if ($this->_getPopup()->getSubscriptionMode() == SubscriptionMode::ALL_LIST) {
            $list = $this->_mailchimpListSource->toOptionHash();
            $list = array_keys($list);
        } elseif ($this->_getPopup()->getSubscriptionMode() == SubscriptionMode::ALL_SELECTED_LIST) {
            $list = $this->_dataHelper->getPopupMailchimpListKeys($this->_getPopup()->getId(), true);
        } elseif (isset($data['mailchimp_list'])) {
            if (!is_array($data['mailchimp_list'])) {
                $data['mailchimp_list'] = [$data['mailchimp_list']];
            }
            $list = $data['mailchimp_list'];
        }
        return $list;
    }

    protected function _createCoupon()
    {
        // create coupon
        $popup = $this->_getPopup();
        $rule = $popup->getCoupon();
        $couponCode = '';
        if ($rule && $rule->getId() && $rule->getIsActive()) {
            $popupData = $popup->getData();
            $ruleData = $rule->getData();

            if ($ruleData['coupon_type'] == Rule::COUPON_TYPE_SPECIFIC) {
                if ($ruleData['use_auto_generation']) {
                    $data = [
                        'rule_id'           => $rule->getId(),

                        'qty'               => 1,
                        'length'            => $popupData['code_length'],
                        'format'            => $popupData['code_format'],
                        'dash'              => $popupData['code_dash'],
                        'prefix'            => $popupData['code_prefix'],
                        'suffix'            => $popupData['code_suffix'],

                        'uses_per_customer' => $ruleData['uses_per_customer'],
                        'uses_per_coupon'   => $ruleData['uses_per_coupon'],
                        'to_date'           => $ruleData['to_date']
                    ];

                    if ($this->_massgenerator->validateData($data)) {
                        /*$couponCode = $this->_massgenerator
                            ->setData($data)
                            ->generateCode();*/
                            //->generatePool()
                            //->getCode();

                        $couponCode = $this->_massgenerator
                            ->setData($data)
                            ->generatePool()
                            ->getGeneratedCodes();

                        if (isset($couponCode[0])) {
                            $couponCode = $couponCode[0];

                            $couponExpDate = $popup->getCouponExpDateTime();

                            if ($couponExpDate) {
                                $couponModel = $this->_couponFactory
                                    ->create()
                                    ->loadByCode($couponCode);

                                if ($couponModel && $couponModel->getId()) {
                                    $popup->setData(
                                        'rule_coupon_expiration_date',
                                        $popup->getCouponExpLocaleDateTime(
                                            $couponExpDate,
                                            true
                                        )
                                    );

                                    $couponModel->setData(
                                        'np_expiration_date',
                                        $couponExpDate
                                    )->save();
                                }
                            }
                        }
                    }
                } else {
                    if (!empty($ruleData['coupon_code'])) {
                        $couponCode = $ruleData['coupon_code'];
                    } elseif (isset($ruleData['coupon'])) {
                        $couponCode = $ruleData['coupon']->getCode();
                    }
                }
            } elseif ($ruleData['coupon_type'] == Rule::COUPON_TYPE_AUTO) {
                // not send type AUTO.
            }
        }

        $popup->setData('rule_coupon_code', $couponCode);

        return $couponCode;
    }

    protected function _sendEmail($email, $couponCode, $data = false)
    {
        $this->_inlineTranslation->suspend();
        $customerName = '';
        if ($data) {
            foreach ($this->_fieldsForEmail as $key) {
                if (!empty($data[$key])) {
                    $customerName .= $data[$key] . ' ';
                }
            }
            $customerName = trim($customerName);
        }
        if (!$data) {
            $customerName = (string)__('Visitor');
        }

        $this->_transportBuilder->setTemplateIdentifier(
            $this->_getPopup()->getEmailTemplate()
        )->setTemplateOptions(
            [
                'area' => Area::AREA_FRONTEND,
                'store' => $this->_storeManager->getStore()->getId(),
            ]
        )->setTemplateVars(
            [
                'code'    => $couponCode,
                'coupon_expiration_date' => $this->_getPopup()->getData('rule_coupon_expiration_date')
            ]
        )->setFrom(
            $this->_scopeConfig->getValue(
                Subscriber::XML_PATH_SUCCESS_EMAIL_IDENTITY,
                ScopeInterface::SCOPE_STORE
            )
        )->addTo(
            $email,
            $customerName
        );
        $transport = $this->_transportBuilder->getTransport();
        $transport->sendMessage();

        $this->_inlineTranslation->resume();

        return $this;
    }

    public function getAdditionalData($data)
    {
        $resData = [];
        if ($this->_dataHelper->moduleEnabled() && $data) {
            $fieldKeys = $this->_dataHelper->getPopupFormFieldsKeys($this->_getPopup()->getId(), true);

            foreach ($fieldKeys as $key) {
                if (array_key_exists($key, $data) && $key != 'email') {
                    $resData['subscriber_' . $key] = $data[$key];
                }
            }

            if (in_array('region', $fieldKeys) && isset($data['country_id'])) {
                $amountCountryRegions = $this->regionFactory->create()
                    ->getCollection()
                    ->addFieldToFilter('country_id', $data['country_id'])
                    ->getSize();

                if ($amountCountryRegions > 0) {
                    if (isset($data['region_id'])) {
                        $region = $this->regionFactory->create()->load($data['region_id']);
                        $resData['subscriber_region'] = $region->getName();
                    }else{
                        $resData['subscriber_region'] = null;
                    }
                }
            }
        }

        return $resData;
    }

    protected function _getHoldItem($email)
    {
        return $this->_resourceConnection->getConnection(Data::SECTION_ID . '_read')
            ->fetchRow(sprintf(
                "SELECT * FROM %s WHERE `email` = '%s'",
                $this->_resourceConnection->getTableName('plumrocket_newsletterpopup_hold'),
                $email
            ));
    }

    protected function _insertHoldItem($email, $postData)
    {
        if (! empty($email)) {
            $integrationLists = [];

            foreach ($this->integrationRepository->getList() as $integration) {
                if ($integration->isEnable()) {
                    $integrationId = $integration->getIntegrationId();
                    $activeLists = $this->getActiveIntegrationLists($integration, $postData);

                    if (! empty($activeLists)) {
                        $integrationLists[$integrationId] = $activeLists;
                    }
                }
            }

            $queryString = sprintf(
                "INSERT INTO `%s` (`email`, `popup_id`, `lists`, `integration_lists`) VALUES ('%s', '%u', '%s', '%s')",
                $this->_resourceConnection->getTableName('plumrocket_newsletterpopup_hold'),
                $email,
                $this->_getPopup()->getId(),
                implode(',', $this->_getActiveMailchimpList($postData)),
                json_encode($integrationLists)
            );

            $this->_resourceConnection->getConnection(Data::SECTION_ID . '_write')->query($queryString);
        }
    }

    protected function _deleteHoldItem($email)
    {
        return $this->_resourceConnection->getConnection(Data::SECTION_ID . '_write')
            ->query(sprintf(
                "DELETE FROM %s WHERE `email` = '%s'",
                $this->_resourceConnection->getTableName('plumrocket_newsletterpopup_hold'),
                $email
            ));
    }

    public function holdSubscribe($email, $data)
    {
        if ($this->_getHoldItem($email)) {
            return false;
        }
        $this->_insertHoldItem($email, $data);
        return $this;
    }

    public function releaseSubscribe($subscriber)
    {
        if (! $this->_dataHelper->moduleEnabled()) {
            return $this;
        }

        $email = $subscriber->getEmail();
        $data = $this->_getHoldItem($email);
        $popupId = ! empty($data['popup_id']) ? (int)$data['popup_id'] : false;
        $this->_popup = $this->_dataHelper->getPopupById($popupId);
        $dataMailChimpLists = ! empty($data['lists']) ? (string)$data['lists'] : false;
        $dataIntegrationLists = ! empty($data['integration_lists']) ? (string)$data['integration_lists'] : false;
        $allowedFields = $this->_dataHelper->getPopupFormFieldsKeys($popupId, false);
        $subscriberData = [];

        foreach ($subscriber->getData() as $key => $value) {
            $key = str_replace('subscriber_', '', $key);

            if (in_array($key, $allowedFields) && !empty($value)) {
                $subscriberData[$key] = $value;
            }
        }

        $subscriberData['mailchimp_list'] = $dataMailChimpLists ? explode(',', $dataMailChimpLists) : [];
        $subscriberData['integration'] = $dataIntegrationLists ? json_decode($dataIntegrationLists, true) : [];

        /* Subscribe */
        $this->subscribe($email, $subscriberData);
        $this->_deleteHoldItem($email);

        return $this;
    }

    public function tryRegisterCustomer($customer, $controller)
    {
        if (!$this->_dataHelper->moduleEnabled()) {
            return false;
        }

        if ($this->_getPopup()->getSignupMethod() != SignupMethod::SIGNUP_AND_REGISTER) {
            return false;
        }

        $customer->save();

        $this->_eventManager->dispatch(
            'customer_register_success',
            ['account_controller' => $controller, 'customer' => $customer]
        );

        if ($customer->isConfirmationRequired() && $this->_dataHelper->getConfig(Subscriber::XML_PATH_CONFIRMATION_FLAG) != 1) {
            $customer->sendNewAccountEmail(
                'confirmation',
                $this->_customerUrl->getAccountUrl(),
                $this->_storeManager->getStore()->getId()
            );
            $this->_messageManager->addSuccess(
                __(
                    'Account confirmation is required. Please, check your email for the confirmation link. To resend the confirmation email please <a href="%1">click here</a>.',
                    $this->_customerUrl->getEmailConfirmationUrl($customer->getEmail())
                )
            );
        } else {
            $customer->sendNewAccountEmail(
                'registered',
                '',
                $this->_storeManager->getStore()->getId()
            );
            $this->_session->setCustomerAsLoggedIn($customer);
            // $this->_session->renew();
        }

        return $customer->getId();
    }

    public function validateCustomer($data)
    {
        if (!$this->_dataHelper->moduleEnabled()) {
            return false;
        }
        $customer = $this->_customerFactory->create()->setId(null);
        $customer->getGroupId();
        $customer->setData($data);

        $fieldKeys = $this->_dataHelper->getPopupFormFieldsKeys($this->_getPopup()->getId(), true);

        if (!in_array('password', $fieldKeys)) {
            $customer->setPassword($this->_generatePassword());
        }
        return $this->_validateCustomer($customer, $fieldKeys)? $customer : false;
    }

    protected function _generatePassword($length = 8)
    {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $mathRandom = new Random;
        return $mathRandom->getRandomString($length, $chars);
    }

    /**
     * @param $customer \Magento\Customer\Model\Customer
     * @param $fieldKeys
     * @return bool
     */
    protected function _validateCustomer($customer, $fieldKeys)
    {
        if (in_array('firstname', $fieldKeys)) {
            if (!\Zend_Validate::is(trim($customer->getFirstname()), 'NotEmpty')) {
                $this->_messageManager->addError(__('The first name cannot be empty.'));
                return false;
            }
        }

        if (in_array('lastname', $fieldKeys)) {
            if (!\Zend_Validate::is(trim($customer->getLastname()), 'NotEmpty')) {
                $this->_messageManager->addError(__('The last name cannot be empty.'));
                return false;
            }
        }

        if (!\Zend_Validate::is($customer->getEmail(), 'EmailAddress')) {
            $this->_messageManager->addError(__('Invalid email address "%1".', $customer->getEmail()));
            return false;
        }

        if (in_array('confirm_email', $fieldKeys)) {
            if ($customer->getEmail() != $customer->getConfirmEmail()) {
                $this->_messageManager->addError(__('Please make sure your emails match.'));
                return false;
            }
        }

        $password = $customer->getPassword();
        if (!$customer->getId() && !\Zend_Validate::is($password, 'NotEmpty')) {
            $this->_messageManager->addError(__('The password cannot be empty.'));
            return false;
        }
        if ('' !== $password && !\Zend_Validate::is($password, 'StringLength', [6])) {
            $this->_messageManager->addError(__('The minimum password length is %1', 6));
            return false;
        }
        if (in_array('confirm_password', $fieldKeys)) {
            $confirmation = $customer->getConfirmation();
            if ($password != $confirmation) {
                $this->_messageManager->addError(__('Please make sure your passwords match.'));
                return false;
            }
        }

        if (in_array('dob', $fieldKeys)) {
            $attribute = $this->_attributeMetadataDataProvider->getAttribute('customer', 'dob');
            if ($attribute->getIsRequired() && '' == trim($customer->getDob())) {
                $this->_messageManager->addError(__('The Date of Birth is required.'));
                return false;
            }
        }

        if (in_array('taxvat', $fieldKeys)) {
            $attribute = $this->_attributeMetadataDataProvider->getAttribute('customer', 'taxvat');
            if ($attribute->getIsRequired() && '' == trim($customer->getTaxvat())) {
                $this->_messageManager->addError(__('The TAX/VAT number is required.'));
                return false;
            }
        }

        if (in_array('gender', $fieldKeys)) {
            $attribute = $this->_attributeMetadataDataProvider->getAttribute('customer', 'gender');
            if ($attribute->getIsRequired() && '' == trim($customer->getGender())) {
                $this->_messageManager->addError(__('Gender is required.'));
                return false;
            }
        }

        return true;
    }

    public function validateAddress($data)
    {
        if (!$this->_dataHelper->moduleEnabled()) {
            return false;
        }
        $address = $this->_customerAddressFactory->create()->setId(null);
        $address->setData($data);

        $fieldKeys = $this->_dataHelper->getPopupFormFieldsKeys($this->_getPopup()->getId(), true);
        return $this->_validateAddress($address, $fieldKeys)? $address : false;
    }

    /**
     * @param $address
     * @param $fieldKeys
     * @return bool
     */
    protected function _validateAddress($address, $fieldKeys)
    {
        if (in_array('street', $fieldKeys)) {
            if (!\Zend_Validate::is($address->getStreet(1), 'NotEmpty')) {
                $this->_messageManager->addError(__('Please enter the street.'));
                return false;
            }
        }

        if (in_array('city', $fieldKeys)) {
            if (!\Zend_Validate::is($address->getCity(), 'NotEmpty')) {
                $this->_messageManager->addError(__('Please enter the city.'));
                return false;
            }
        }

        if (in_array('telephone', $fieldKeys)) {
            if (!\Zend_Validate::is($address->getTelephone(), 'NotEmpty')) {
                $this->_messageManager->addError(__('Please enter the telephone number.'));
                return false;
            }
        }

        if (in_array('postcode', $fieldKeys)) {
            $_havingOptionalZip = $this->_directoryHelper->getCountriesWithOptionalZip();
            if (!in_array($address->getCountryId(), $_havingOptionalZip) && !\Zend_Validate::is($address->getPostcode(), 'NotEmpty')) {
                $this->_messageManager->addError(__('Please enter the zip/postal code.'));
                return false;
            }
        }

        if (in_array('country_id', $fieldKeys)) {
            if (!\Zend_Validate::is($address->getCountryId(), 'NotEmpty')) {
                $this->_messageManager->addError(__('Please enter the country.'));
                return false;
            }
        }

        if (in_array('region', $fieldKeys)) {
            if ($address->getCountryModel()->getRegionCollection()->getSize()
                && !\Zend_Validate::is($address->getRegionId(), 'NotEmpty')
            ) {
                $this->_messageManager->addError(__('Please enter the state/province.'));
                return false;
            }
        }

        if (in_array('agreement', $fieldKeys)) {
            if (!\Zend_Validate::is($address->getAgreement(), 'NotEmpty')) {
                $this->_messageManager->addError(__('Agreement is required.'));
                return false;
            }
        }

        return true;
    }

    public function cancel()
    {
        if (!$this->_dataHelper->moduleEnabled()) {
            return false;
        }
        if ($this->_getPopup()->getId() > 0) {
            $this->_addHistory(Action::CANCEL, '');
        }
    }

    public function history($actionText)
    {
        if (!$this->_dataHelper->moduleEnabled()) {
            return false;
        }
        if ($this->_getPopup()->getId() > 0) {
            $this->_addHistory(Action::OTHER, '', '', [
                'action_text' => $actionText
            ]);
        }
    }

    private function _getPopup()
    {
        if (null === $this->_popup) {
            $this->_popup = $this->_dataHelper->getCurrentPopup();
        }
        return $this->_popup;
    }

    private function _addHistory($action, $email = '', $couponCode = '', $additional = [])
    {
        $data = array_merge([
            'popup_id'          => (int)$this->_getPopup()->getId(),
            'action'            => $action,
            'customer_email'    => $email,
            'coupon_code'       => $couponCode,
        ], $additional);

        $this->_historyFactory->create()
            ->setData($data)
            ->save();

        // increment statistic
        $this->_getPopup()->setData('views_count', $this->_getPopup()->getData('views_count') + 1);
        if ($action == Action::SUBSCRIBE) {
            $this->_getPopup()->setData('subscribers_count', $this->_getPopup()->getData('subscribers_count') + 1);
        }
        $this->_getPopup()->save();

        // increment previous shows
        // $session = Mage::getSingleton('core/session');
        if (!$prevPopups = $this->_session->getData('prnewsletterpopup_prev_popups')) {
            $prevPopups = [];
        }
        $prevPopups[$this->_getPopup()->getId()] = true;
        $this->_session->setData('prnewsletterpopup_prev_popups', $prevPopups);
    }
}
