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
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Newsletterpopup\Controller\AccessToken;

class ConstantContact extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Plumrocket\Newsletterpopup\Model\Integration\Authorization\ConstantContact
     */
    private $auth;

    /**
     * @var \Plumrocket\Newsletterpopup\Helper\Data
     */
    private $dataHelper;

    /**
     * @param \Magento\Framework\App\Action\Context                                       $context
     * @param \Plumrocket\Newsletterpopup\Model\Integration\Authorization\ConstantContact $auth
     * @param \Plumrocket\Newsletterpopup\Helper\Data                                     $dataHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Plumrocket\Newsletterpopup\Model\Integration\Authorization\ConstantContact $auth,
        \Plumrocket\Newsletterpopup\Helper\Data $dataHelper
    ) {
        $this->auth = $auth;
        $this->dataHelper = $dataHelper;
        parent::__construct($context);
    }

    /**
     * Generate constant contact access token action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $code = $this->getRequest()->getParam('code');

        if (! $this->dataHelper->moduleEnabled() || ! $code) {
            return $this->_redirect('/');
        }

        $result = $this->auth->generateAccessToken($code);
        $responseBody = '
            <script type="application/javascript">
                var accessTokenMessageblock = opener.document.querySelector("fieldset#prnewsletterpopup_integration_constantcontact #row_prnewsletterpopup_integration_constantcontact_access_token .generate-token-message-block");

                if (accessTokenMessageblock) {
                    accessTokenMessageblock.parentNode.removeChild(accessTokenMessageblock);
                }

                var elem = document.createElement("div"); elem.setAttribute("class", "generate-token-message-block");';

        if (true === $result) {
            $responseBody .= 'var content = "<div id=\'result_container_constantcontact\' class=\'message message-success success\' style=\'background: none; color: green;\'><b>' . __('Access token successfully generated!') . '</b></div>";
                opener.document.querySelector("fieldset#prnewsletterpopup_integration_constantcontact input#prnewsletterpopup_integration_constantcontact_access_token").setAttribute("value", "' . $this->auth->getAccessToken() . '");';
        } else {
            $errorMessage = (isset($result['error_description'])
                ? $result['error_description']
                : __('Something went wrong.'));

            $responseBody .= 'var content = "<div id=\'result_container_constantcontact\' class=\'message message-error error\' style=\'background: none; color: red;\'><b>' . __('Connection Error!') . '</b><p>' . __($errorMessage) . '</p></div>";';
        }

        $responseBody .= "var buttonElem = opener.document.querySelector('fieldset#prnewsletterpopup_integration_constantcontact #row_prnewsletterpopup_integration_constantcontact_access_token button.integration-generate-token');
                elem.innerHTML = content;
                buttonElem.parentNode.insertBefore(elem, buttonElem.nextSibling);
                
                var buttonsToEnable = [
                    'prnewsletterpopup_integration_constantcontact_test_connection',
                    'prnewsletterpopup_integration_constantcontact_custom_fields',
                ];

                buttonsToEnable.forEach(function (buttonId) {
                    var btn = opener.document.getElementById(buttonId);
                    btn.classList.remove('disabled')
                    btn.disabled = false;
                });

                window.close();
            </script>";

        return $this->getResponse()->setBody($responseBody)->sendResponse();
    }
}
