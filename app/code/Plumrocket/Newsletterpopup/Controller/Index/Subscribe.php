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
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Newsletterpopup\Controller\Index;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Message\MessageInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Validator\Exception as ValidatorException;
use Plumrocket\Newsletterpopup\Helper\Data;
use Plumrocket\Newsletterpopup\Block\Popup\Fields\Dob;
use Plumrocket\Newsletterpopup\Model\Subscriber;
use Psr\Log\LoggerInterface;
use Magento\Framework\Registry;

class Subscribe extends Action
{
    /**
     * @var \Plumrocket\Newsletterpopup\Model\Subscriber
     */
    private $subscriber;

    /**
     * @var \Plumrocket\Newsletterpopup\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    private $serializer;

    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * Subscribe constructor.
     *
     * @param \Magento\Backend\App\Action\Context              $context
     * @param \Plumrocket\Newsletterpopup\Model\Subscriber     $subscriber
     * @param \Plumrocket\Newsletterpopup\Helper\Data          $dataHelper
     * @param \Psr\Log\LoggerInterface                         $logger
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     * @param \Magento\Framework\Registry                      $coreRegistry
     */
    public function __construct(
        Context $context,
        Subscriber $subscriber,
        Data $dataHelper,
        LoggerInterface $logger,
        SerializerInterface $serializer,
        Registry $coreRegistry
    ) {
        parent::__construct($context);
        $this->subscriber = $subscriber;
        $this->dataHelper = $dataHelper;
        $this->logger = $logger;
        $this->serializer = $serializer;
        $this->coreRegistry = $coreRegistry;
    }

    public function execute()
    {
        try {
            if (!$this->dataHelper->moduleEnabled()) {
                throw new ValidatorException(__('The Plumrocket Newsletter Popup Module is disabled.'));
            }

            $recaptchaResponse = $this->getRequest()->getParam('g-recaptcha-response');
            $popupId = $this->getRequest()->getParam('id');
            if (! $this->dataHelper->reCaptchaVerify($recaptchaResponse, $popupId)) {
                throw new ValidatorException(__('Please click on reCAPTCHA checkbox.'));
            }

            $email = $this->getRequest()->getParam('email');
            if (!\Zend_Validate::is($email, 'EmailAddress')) {
                throw new ValidatorException(__('Please enter a valid email address.'));
            }

            if ($this->dataHelper->getConfig(Data::SECTION_ID . '/disposable_emails/disable')) {
                $_email = preg_replace('#[[:space:]]#', '', $email);
                preg_match('#@([\w\-.]+$)#is', $_email, $domain);
                if (!empty($domain[1])) {
                    preg_match(
                        '#(?:^|[\s,]+)'. preg_quote($domain[1]) . '(?:$|[\s,]+)#i',
                        $this->dataHelper->getConfig(Data::SECTION_ID . '/disposable_emails/domains'),
                        $math
                    );
                    if (!empty($math)) {
                        throw new ValidatorException(__('This email address provider is blocked. Please try again with different email address.'));
                    }
                }
            }

            $subscriber = $this->subscriber->load($email, 'subscriber_email');
            if ((int)$subscriber->getId() !== 0) {
                throw new ValidatorException(__('This email address is already assigned to another user.'));
            }

            $inputData = $this->getRequest()->getParams();
            // Prepare DOB.
            if (empty($inputData['dob'])
                && !empty($inputData['month'])
                && !empty($inputData['day'])
                && !empty($inputData['year'])
            ) {
                $dateMapping = $this->_view
                    ->getLayout()
                    ->createBlock(Dob::class)
                    ->getDateMapping(false);
                $inputData['dob'] = sprintf(
                    $dateMapping,
                    (int) $inputData['month'],
                    (int) $inputData['day'],
                    (int) $inputData['year']
                );
            }

            // Prepare mailchimp lists if they was passed through integration data
            if (isset($inputData['integration']['mailchimp'])) {
                $inputData['mailchimp_list'] = $inputData['integration']['mailchimp'];
            }

            $subscriber->customSubscribe($email, $this, $inputData);
        } catch (ValidatorException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            // $this->messageManager->addError($e->getMessage());
            $this->messageManager->addErrorMessage(__('Unknown Error'));
            $this->logger->error(__METHOD__ . ' ' . $e->getMessage());
        }

        $data = [
            'error' => 0,
            'messages' => [],
            'hasSuccessTextPlaceholders' => false,
        ];

        $messages = $this->messageManager->getMessages(true);
        foreach ($messages->getItems() as $message) {
            if ($message->getType() !== MessageInterface::TYPE_SUCCESS) {
                $data['error'] = 1;
                $this->coreRegistry->register('prgdpr_skip_save_consents', true);
            }
            if (!array_key_exists($message->getType(), $data['messages'])) {
                $data['messages'][$message->getType()] = [];
            }
            $data['messages'][$message->getType()][] = $message->getText();
        }

        if ($popupId = $this->getRequest()->getParam('id')) {
            $data['hasSuccessTextPlaceholders'] = $this->dataHelper
                ->getPopupById($popupId)
                ->hasSuccessTextPlaceholders();
        }

        $this->getResponse()
            ->setHeader('Content-type', 'application/json')
            ->clearHeader('Location')
            // ->clearRawHeader('Location')
            ->setHttpResponseCode(200)
            ->setBody($this->serializer->serialize($data));
    }
}
