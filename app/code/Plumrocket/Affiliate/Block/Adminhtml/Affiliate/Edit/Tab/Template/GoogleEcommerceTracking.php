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
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Block\Adminhtml\Affiliate\Edit\Tab\Template;

use \Magento\GoogleAnalytics\Helper\Data as GoogleAnalyticsData;

class GoogleEcommerceTracking extends AbstractNetwork
{
    /**
     * @var \Magento\GoogleAnalytics\Helper\Data
     */
    protected $googleAnalyticsData;

    /**
     * @param \Magento\Backend\Block\Template\Context                           $context
     * @param \Plumrocket\Affiliate\Model\IncludeonFactory                      $includeonFactory
     * @param \Plumrocket\Affiliate\Block\Adminhtml\System\Config\Form\Version  $version
     * @param GoogleAnalyticsData                                               $googleAnalyticsData
     * @param array                                                             $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context                             $context,
        \Plumrocket\Affiliate\Model\IncludeonFactory                        $includeonFactory,
        \Plumrocket\Affiliate\Block\Adminhtml\System\Config\Form\Version    $version,
        GoogleAnalyticsData                                                 $googleAnalyticsData,
        array $data = []
    ) {
        parent::__construct($context, $includeonFactory, $version, $data);
        $this->googleAnalyticsData = $googleAnalyticsData;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareForm($form)
    {
        $affiliate  = $this->getAffiliate();

        $fieldset = $form->addFieldset('section_bodyend', ['legend' => __('Affiliate Script'), 'class' => 'fieldset-wide']);

        $fieldset->addField(
            'section_head_includeon_id',
            'hidden',
            [
                'name'      => 'section_head_includeon_id',
                'value'     => $this->getIncludeonByKey('checkout_success')->getId(),
            ]
        );

        if (!$this->googleAnalyticsData->isGoogleAnalyticsAvailable()) {
            $url = $this->getUrl('adminhtml/system_config/edit', ['section' => 'google']);

            $fieldset->addField(
                'note',
                'note',
                [
                    'label'     => __('Google Analytics API'),
                    'required'  => true,
                    'text'      => __('Google Analytics is disabled in Magento Configuration.').'<br/>'.
                        __('Please enable Google Analytics in order for Ecommerce Tracking to work.').'<br/>'.
                        '<a title="Google Analytics API" href="'.$url.'" onclick="window.open(this.href); return false;"><img src="'.$this->getViewFileUrl('Plumrocket_Affiliate::images/google_api.png').'" style="border: 2px solid #d6d6d6; margin-top: 10px; margin-bottom: 10px" /></a><br/>'.
                        __('Go to System -> Configuration -> Google API (or <a href="'.$url.'" target="_blank" >click here</a>). Enter your Google Analytics Account Number, set Enable = "Yes" and press "Save Config".')
                ]
            );
        } else {
            $fieldset->addField(
                'note',
                'note',
                [
                    'label'     => __('Google Analytics API'),
                    'required'  => true,
                    'text'      => __('Good news! Google Analytics is enabled in Magento Configuration.').'<br/>'.
                        __('Your Account Number is <strong>%1</strong>.', $this->_scopeConfig->getValue(GoogleAnalyticsData::XML_PATH_ACCOUNT)).'<br/><br/>'.
                        __('Google Analytics Ecommerce Tracking is ready to report ecommerce activity.').'<br/>'.
                        __('Make sure to enable ecommerce tracking on the view (profile) settings page for your website in <a href="http://www.google.com/analytics/" target="_blank" >Google Analytics account</a>. For manual please refer to our <a href="%1"  target="_blank">online documentation</a>.', $this->getWikiLink())
                ]
            );
        }

        return $this;
    }
}
