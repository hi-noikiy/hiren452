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

declare(strict_types=1);

namespace Plumrocket\AmpEmail\Model\Template;

/**
 * Class AddAmpAlternatives
 *
 * @since 1.0.1
 */
class AddAmpAlternatives implements \Plumrocket\AmpEmail\Model\AddAmpAlternativesToTemplatesInterface
{
    /**
     * @var \Magento\Email\Model\ResourceModel\Template\CollectionFactory
     */
    private $templateCollectionFactory;

    /**
     * @var \Magento\Email\Model\ResourceModel\Template
     */
    private $resourceEmailTemplate;

    /**
     * @var \Plumrocket\AmpEmail\Model\Template\Config
     */
    private $ampTemplateConfig;

    /**
     * @var \Plumrocket\AmpEmail\Api\AmpTemplateProviderInterface
     */
    private $ampTemplateProvider;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * AddAmpAlternatives constructor.
     *
     * @param \Magento\Email\Model\ResourceModel\Template\CollectionFactory $templateCollectionFactory
     * @param \Magento\Email\Model\ResourceModel\Template                   $resourceEmailTemplate
     * @param \Plumrocket\AmpEmail\Model\Template\Config                    $ampTemplateConfig
     * @param \Plumrocket\AmpEmail\Api\AmpTemplateProviderInterface         $ampTemplateProvider
     * @param \Psr\Log\LoggerInterface                                      $logger
     */
    public function __construct(
        \Magento\Email\Model\ResourceModel\Template\CollectionFactory $templateCollectionFactory,
        \Magento\Email\Model\ResourceModel\Template $resourceEmailTemplate,
        \Plumrocket\AmpEmail\Model\Template\Config $ampTemplateConfig,
        \Plumrocket\AmpEmail\Api\AmpTemplateProviderInterface $ampTemplateProvider,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->templateCollectionFactory = $templateCollectionFactory;
        $this->resourceEmailTemplate = $resourceEmailTemplate;
        $this->ampTemplateConfig = $ampTemplateConfig;
        $this->ampTemplateProvider = $ampTemplateProvider;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function execute() : int
    {
        $countOfChangedTemplates = 0;

        $templatesIds = array_column($this->ampTemplateConfig->getAvailableTemplates(), 'value');
        /** @var \Magento\Email\Model\ResourceModel\Template\Collection $magentoTemplatesCollection */
        $magentoTemplatesCollection = $this->templateCollectionFactory->create();

        $magentoTemplatesCollection
            ->addFieldToFilter('orig_template_code', ['in' => $templatesIds])
            ->addFieldToFilter('pramp_email_content', ['null' => true])
            ->addFieldToFilter('pramp_email_styles', ['null' => true]);

        try {
            /** @var \Magento\Email\Model\Template $emailTemplate */
            foreach ($magentoTemplatesCollection->getItems() as $emailTemplate) {
                $ampTemplate = $this->ampTemplateProvider->getTemplate($emailTemplate->getOrigTemplateCode());
                $emailTemplate->setPrampEmailContent($ampTemplate->getPrampEmailContent());
                $emailTemplate->setPrampEmailStyles($ampTemplate->getPrampEmailStyles());
                $this->resourceEmailTemplate->save($emailTemplate);
                $countOfChangedTemplates++;
            }
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $this->logger->critical($e);
        } catch (\Magento\Framework\Exception\AlreadyExistsException $e) {
            $this->logger->critical($e);
        }

        return $countOfChangedTemplates;
    }
}
