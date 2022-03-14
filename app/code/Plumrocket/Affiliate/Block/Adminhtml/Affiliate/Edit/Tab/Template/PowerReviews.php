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
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Block\Adminhtml\Affiliate\Edit\Tab\Template;

class PowerReviews extends AbstractNetwork
{
    /**
     * {@inheritdoc}
     */
    public function prepareForm($form)
    {
        /**
         * @var \Plumrocket\Affiliate\model\Affiliate\PowerReviews $affiliate
         */
        $affiliate  = $this->getAffiliate();

        $fieldset = $form->addFieldset('section_bodybegin', ['legend' => __('Affiliate Script - Pay Per Sale (PPS) or Cost Per Sale (CPS) Program'), 'class' => 'fieldset-wide']);

        $fieldset->addField(
            'additional_data_merchant_group_id',
            'text',
            [
                'name'      => 'additional_data[merchant_group_id]',
                'label'     => 'Merchant Group ID',
                'required'  => true,
                'class'     => 'validate-digits',
                'value'     => $affiliate->getMerchantGroupId(),
                'note'      => 'Place the applicable PowerReviews Merchant Group ID here. Consult your account contact or assigned integrator if in any doubt.',
            ]
        );

        $fieldset->addField(
            'additional_data_merchant_id',
            'text',
            [
                'name'     => 'additional_data[merchant_id]',
                'label'    => 'Merchant ID',
                'required' => true,
                'class'    => 'validate-digits',
                'value'    => $affiliate->getMerchantId(),
                'note'     => 'Place the applicable PowerReviews Merchant ID here. Consult your account contact or assigned integrator if in any doubt.',
            ]
        );

        // TODO: note
        $fieldset->addField(
            'additional_data_locale',
            'text',
            [
                'name'     => 'additional_data[locale]',
                'label'    => 'Locale',
                'required' => true,
                'value'    => $affiliate->getLocale(),
                'note'     => 'Please, specify the locale of your website. For example: en_US',
            ]
        );

        $fieldset->addField(
            'section_bodybegin_includeon_id',
            'hidden',
            [
                'name'  => 'section_bodybegin_includeon_id',
                'value' => $this->getIncludeonByKey('checkout_success')->getId(),
            ]
        );

        return $this;
    }
}
