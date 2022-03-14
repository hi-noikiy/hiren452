<?php
/**
 * Copyright (c) 2020, Zillion Insurance Services, Inc.
 * All rights reserved.
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *   * Redistributions of source code must retain the above copyright notice,
 *     this list of conditions and the following disclaimer.
 *   * Redistributions in binary form must reproduce the above copyright notice,
 *     this list of conditions and the following disclaimer in the documentation
 *     and/or other materials provided with the distribution.
 *   * Neither the name of Zend Technologies USA, Inc. nor the names of its
 *     contributors may be used to endorse or promote products derived from this
 *     software without specific prior written permission.
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @package myzillion
 * @subpackage module-simplified-insurance
 * @author Serfe <info@serfe.com>
 * @copyright 2020 Zillion Insurance Services, Inc.
 * @since 1.0.0
 */

namespace MyZillion\SimplifiedInsurance\Plugin\Magento\Shipping\Block\Adminhtml;

use MyZillion\SimplifiedInsurance\Model\ResourceModel\ShipmentPostRequest\Collection as ShipmentPostCollection;

/**
 * Class view render button
 */
class View
{
    const BUTTON_LABEL = 'Zillion Order Request';

    /**
     * @var ShipmentPostCollection
     */
    private $shipmentPostRequestCollection;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * Construct
     *
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param ShipmentPostCollection $shipmentPostRequestCollection
     */
    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager,
        ShipmentPostCollection $shipmentPostRequestCollection
    ) {
        $this->messageManager = $messageManager;
        $this->shipmentPostRequestCollection = $shipmentPostRequestCollection;
    }

    /**
     * Check if a resend should be done
     *
     * @param \Magento\Shipping\Block\Adminhtml\View $view
     * @param  string $layout
     * @return void
     */
    public function beforeSetLayout(\Magento\Shipping\Block\Adminhtml\View $view, $layout)
    {
        $params = [
            'id' => $view->getShipment()->getId(),
        ];

        $message = __('The shipping information will be sent to the Zillion API. Do you want to continue?');

        $url = $view->getUrl('myzillion/postOrder/send', $params);

        if (!$this->checkShipmentInformationSent($view->getShipment()->getId())) {
            $label = self::BUTTON_LABEL;
            $view->addButton(
                'shipping_zillion_send_post_order',
                [
                    'label'   => __($label),
                    'onclick' => sprintf("confirmSetLocation('%s', '%s')", $message, $url),
                ]
            );
        }
    }

    /**
     * Check if a resend should be done
     *
     * @param integer $shipmentId
     * @return boolean
     */
    protected function checkShipmentInformationSent($shipmentId)
    {
        // Check if shipment information was sent into Zillion
        $shipmentPostRequest = $this->shipmentPostRequestCollection
            ->addFieldToFilter('shipment_id', $shipmentId)
            ->getFirstItem();
        $shipmentData = $shipmentPostRequest->getData();

        // If data not exists or is success then don't display the button
        if (!$shipmentData || $shipmentData && $shipmentData['post_order_status'] === 'success') {
            return true;
        }

        $msg = 'The current shipment information was not sent to Zillion. Please use the "';
        $msg .= self::BUTTON_LABEL . '" action button to sent it.';
        $this->messageManager->addError(__($msg));
        return false;
    }
}
