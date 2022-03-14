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
 * @package     Plumrocket_AmpEmail
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\AmpEmail\Controller\Adminhtml\Template;

/**
 * Class Load
 *
 * @package Plumrocket\AmpEmail\Controller\Adminhtml\Template
 */
class Load extends \Magento\Backend\App\Action
{
    /**
     * @var \Plumrocket\AmpEmail\Api\AmpTemplateProviderInterface
     */
    private $ampTemplateProvider;

    /**
     * Load constructor.
     *
     * @param \Magento\Backend\App\Action\Context                   $context
     * @param \Plumrocket\AmpEmail\Api\AmpTemplateProviderInterface $ampTemplateProvider
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Plumrocket\AmpEmail\Api\AmpTemplateProviderInterface $ampTemplateProvider
    ) {
        parent::__construct($context);
        $this->ampTemplateProvider = $ampTemplateProvider;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $result */
        $result = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON);

        $templateId = $this->getRequest()->getParam('template_id');

        if (! empty($templateId)) {
            try {
                $template = $this->ampTemplateProvider->getTemplate($templateId);

                $resultData = [
                    'success' => true,
                    'content' => $template->getPrampEmailContent(),
                    'style' => $template->getPrampEmailStyles(),
                ];
            } catch (\Magento\Framework\Exception\NoSuchEntityException $noSuchEntityException) {
                $resultData = [
                    'success' => false,
                    'message' => $noSuchEntityException->getMessage(),
                ];
            }
        } else {
            $resultData = [
                'success' => false,
                'message' => __('Please, choose template to load.'),
            ];
        }

        return $result->setData($resultData);
    }
}
