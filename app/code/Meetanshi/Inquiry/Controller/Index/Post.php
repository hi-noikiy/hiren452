<?php

namespace Meetanshi\Inquiry\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\Filesystem;
use Meetanshi\Inquiry\Model\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Meetanshi\Inquiry\Helper\Data;
use Meetanshi\Inquiry\Model\InquiryFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\DataObject;
use Magento\Framework\App\Area;
use Magento\Directory\Model\Country;
use Magento\Customer\Model\Customer;

class Post extends Action
{
    protected $fileUploadFactory;
    protected $fileSystem;
    protected $transportBuilder;
    protected $inlineTranslation;
    protected $scopeConfig;
    protected $inquiry;
    protected $helper;
    protected $storeManager;
    protected $country;
    protected $version;
    protected $customer;

    public function __construct(
        Context $context,
        UploaderFactory $fileUploaderFactory,
        Filesystem $fileSystem,
        TransportBuilder $transportBuilder,
        StateInterface $inlineTranslation,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        InquiryFactory $inquiry,
        Country $country,
        Data $helper,
        Customer $customer
    )
    {
        $this->fileUploadFactory = $fileUploaderFactory;
        $this->fileSystem = $fileSystem;
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->inquiry = $inquiry;
        $this->country = $country;
        $this->helper = $helper;
        $this->customer = $customer;
        parent::__construct($context);
    }

    public function execute()
    {
        $post = $this->getRequest()->getPostValue();

        if (!$post) {
            $url = $this->storeManager->getStore()->getBaseUrl() . $this->helper->getUrlKey();
            return $this->_redirect($url);
        }

        if ($this->helper->getRecaptchaEnable()) {
            $token = $this->_request->getParam('g-recaptcha-response');
            $validation = $this->helper->validate($token);
            if (!$validation['success']) {
                $this->messageManager->addErrorMessage(__($validation['error']));
                $url = $this->storeManager->getStore()->getBaseUrl() . $this->helper->getUrlKey();
                return $this->_redirect($url);
            }
        }

        try {
            $model = $this->inquiry->create();
            $model->setFirstName($post['firstname']);
            $model->setLastName($post['lastname']);
            $model->setEmail($post['email']);
            $model->setCompanyName($post['companyname']);
            $model->setCity($post['city']);
            $model->setZipPostalCode($post['zippostalcode']);
            $countryName = $this->country->load($post['country_id'])->getName();
            $model->setCountry($countryName);

            if (array_key_exists('region', $post)):
                $model->setState($post['region']);
                $state = $post['region'];
            else:
                $model->setState($post['state']);
                $state = $post['state'];
            endif;
            $model->setContactNumber($post['telephone']);
            $model->setBusinessDescription($post['businessdescription']);
            $model->setStoreView($this->storeManager->getStore()->getId());

            if ($this->customerExists($post['email'])) {
                $model->setIsCustomerCreated(1);
            }

            if (!empty($post['taxvatnumber'])) :
                $model->setTaxVatNumber($post['taxvatnumber']);
            endif;
            if (!empty($post['address'])) :
                $model->setAddress($post['address']);
            endif;
            if (!empty($post['website'])) :
                $model->setWebsite($post['website']);
            endif;
            if (!empty($post['datetime'])) :
                $model->setCreatedAt($post['datetime']);
            endif;
            if (!empty($post['extrafield1'])) :
                $model->setExtraField1($post['extrafield1']);
            endif;
            if (!empty($post['extrafield2'])) :
                $model->setExtraField2($post['extrafield2']);
            endif;
            if (!empty($post['extrafield3'])) :
                $model->setExtraField3($post['extrafield3']);
            endif;
            $files = $this->getRequest()->getFiles('uploadfiles');
            $filename = "";
            $name = "";
            $pth = "";
            $allowedType = $this->helper->getAllowedFileTypes();
            $allowedType = explode(",", $allowedType);
            $i = 0;
            $flag = false;

            if (isset($files) && $files != null) {
                foreach ($files as $file) {
                    if ($file['name'] != '') {
                        $uploader = $this->fileUploadFactory->create(['fileId' => 'uploadfiles[' . $i . ']']);
                        $uploader->setAllowedExtensions($allowedType);
                        $uploader->setAllowRenameFiles(true);
                        $uploader->setFilesDispersion(false);
                        $path = $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('inquiry/');
                        $result = $uploader->save($path);

                        if (sizeof($files) == 1) {
                            $filename = 'inquiry/' . $result['file'];
                        } else {
                            $filename .= $result['file'] . ',';
                        }
                        $name .= $result['file'] . ",";
                        $pth .= $path . $result['file'] . ",";
                        $i++;
                        $flag = true;
                    }
                }
            }
            if ($flag) {
                $model->setFiles($filename);
            }
            $model->save();
            $emailObject = $post;
            $emailObject['uploadfiles'] = $name;
            $emailObject['country'] = $countryName;
            $emailObject['region'] = $state;
            $postObject = new DataObject();
            $postObject->setData($emailObject);
            $to = $this->helper->getOwnerEmail();
            $fromEmail = $post['email'];
            $fromName = $post['firstname'] . " " . $post['lastname'];
            $template = "dealer_inquiry_admin_template";
            $template = $this->helper->getOwnerEmailTemplate();

            if ($pth != "") {
                $this->sendEmailAttachment($fromEmail, $fromName, $to, $postObject, $template, $pth, $name);
            } else {
                $this->sendInquiryEmail($fromEmail, $fromName, $to, $postObject, $template);
            }

            $fromName = $this->helper->getSenderEmailName($this->helper->getCustomerEmailSender());
            $fromEmail = $this->helper->getSenderEmailAddress($this->helper->getCustomerEmailSender());
            $to = $post['email'];
            $template = "dealer_inquiry_customer_template";
            $template = $this->helper->getCustomerEmailTemplate();
            $this->sendInquiryEmail($fromEmail, $fromName, $to, $postObject, $template);
            $this->messageManager->addSuccessMessage(__($this->helper->getSuccessMessage()));
            $url = $this->storeManager->getStore()->getBaseUrl() . $this->helper->getUrlKey();
            return $this->_redirect($url);
        } catch (\Exception $e) {
            $this->helper->printLog($e->getMessage());
            $this->messageManager->addErrorMessage(__('We can’t process your request right now'));
            $url = $this->storeManager->getStore()->getBaseUrl() . $this->helper->getUrlKey();
            return $this->_redirect($url);
        }
    }

    public function customerExists($email, $websiteId = null)
    {
        $baseCustomer = $this->customer;
        $baseCustomer->setWebsiteId($this->storeManager->getStore()->getWebsiteId());
        $baseCustomer->loadByEmail($email);
        $customer = $this->customer;
        if ($websiteId) {
            $customer->setWebsiteId($websiteId);
        } elseif ($baseCustomer->getWebsiteId()) {
            $customer->setWebsiteId($baseCustomer->getWebsiteId());
        } else {
            return false;
        }
        $customer->loadByEmail($email);
        if ($customer->getId()) {
            return true;
        }
        return false;
    }

    public function sendEmailAttachment($fromEmail, $fromName, $to, $postObject, $template, $pth, $name)
    {
        $from = ['email' => $fromEmail, 'name' => $fromName];
        $this->inlineTranslation->suspend();
        $templateOptions = [
            'area' => Area::AREA_FRONTEND,
            'store' => $this->storeManager->getStore()->getId()
        ];
        try {
            $path = explode(",", $pth);
            $name = explode(",", $name);

            if (sizeof($name) == 2) {
                $transport = $this->transportBuilder
                    ->setTemplateIdentifier($template)
                    ->setTemplateOptions($templateOptions)
                    ->setTemplateVars(['data' => $postObject])
                    ->setFrom($from)
                    ->addTo($to)
                    ->addAttachment($path[0], $name[0])
                    ->getTransport();

                $transport->sendMessage();
            } elseif (sizeof($name) == 3) {
                $transport = $this->transportBuilder->setTemplateIdentifier($template)
                    ->setTemplateOptions($templateOptions)
                    ->setTemplateVars(['data' => $postObject])
                    ->setFrom($from)
                    ->addTo($to)
                    ->addAttachment($path[0], $name[0])
                    ->addAttachment($path[1], $name[1])
                    ->getTransport();

                $transport->sendMessage();
            } elseif (sizeof($name) == 4) {
                $transport = $this->transportBuilder->setTemplateIdentifier($template)
                    ->setTemplateOptions($templateOptions)
                    ->setTemplateVars(['data' => $postObject])
                    ->setFrom($from)
                    ->addTo($to)
                    ->addAttachment($path[0], $name[0])
                    ->addAttachment($path[1], $name[1])
                    ->addAttachment($path[2], $name[2])
                    ->getTransport();

                $transport->sendMessage();
            } elseif (sizeof($name) == 5) {
                $transport = $this->transportBuilder->setTemplateIdentifier($template)
                    ->setTemplateOptions($templateOptions)
                    ->setTemplateVars(['data' => $postObject])
                    ->setFrom($from)
                    ->addTo($to)
                    ->addAttachment($path[0], $name[0])
                    ->addAttachment($path[1], $name[1])
                    ->addAttachment($path[2], $name[2])
                    ->addAttachment($path[3], $name[3])
                    ->getTransport();

                $transport->sendMessage();
            } elseif (sizeof($name) == 6) {
                $transport = $this->transportBuilder->setTemplateIdentifier($template)
                    ->setTemplateOptions($templateOptions)
                    ->setTemplateVars(['data' => $postObject])
                    ->setFrom($from)
                    ->addTo($to)
                    ->addAttachment($path[0], $name[0])
                    ->addAttachment($path[1], $name[1])
                    ->addAttachment($path[2], $name[2])
                    ->addAttachment($path[3], $name[3])
                    ->addAttachment($path[4], $name[4])
                    ->getTransport();

                $transport->sendMessage();
            }
            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
            $this->helper->printLog($e->getMessage());
        }
    }

    public function sendInquiryEmail($fromEmail, $fromName, $to, $postObject, $template)
    {
        $from = ['email' => $fromEmail, 'name' => $fromName];
        $this->inlineTranslation->suspend();
        $templateOptions = [
            'area' => Area::AREA_FRONTEND,
            'store' => $this->storeManager->getStore()->getId()
        ];
        try {
            $transport = $this->transportBuilder->setTemplateIdentifier($template)
                ->setTemplateOptions($templateOptions)
                ->setTemplateVars(['data' => $postObject])
                ->setFrom($from)
                ->addTo($to)
                ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
            $this->helper->printLog($e->getMessage());
        }
    }
}
