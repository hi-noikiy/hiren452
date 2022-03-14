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
 * @package     Plumrocket AmpEmail
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */
declare(strict_types=1);

namespace Plumrocket\AmpEmail\Block\Adminhtml\Template\Grid\Filter;

use Plumrocket\AmpEmail\Model\Email\AmpTemplateInterface;

/**
 * Class AmpEnabled
 *
 * @package Plumrocket\AmpEmail\Block\Adminhtml\Template\Grid\Filter
 * @method mixed getValue()
 */
class AmpStatus extends \Magento\Backend\Block\Widget\Grid\Column\Filter\Select
{
    /**
     * @var \Magento\Email\Model\ResourceModel\Template\CollectionFactory
     */
    private $templateCollectionFactory;

    /**
     * @var \Plumrocket\AmpEmail\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Plumrocket\AmpEmail\Api\AmpTemplateProviderInterface
     */
    private $ampTemplateProvider;

    /**
     * AmpStatus constructor.
     *
     * @param \Magento\Backend\Block\Context                                $context
     * @param \Magento\Framework\DB\Helper                                  $resourceHelper
     * @param \Magento\Email\Model\ResourceModel\Template\CollectionFactory $templateCollectionFactory
     * @param \Plumrocket\AmpEmail\Helper\Data                              $dataHelper
     * @param \Plumrocket\AmpEmail\Api\AmpTemplateProviderInterface         $ampTemplateProvider
     * @param array                                                         $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Framework\DB\Helper $resourceHelper,
        \Magento\Email\Model\ResourceModel\Template\CollectionFactory $templateCollectionFactory,
        \Plumrocket\AmpEmail\Helper\Data $dataHelper,
        \Plumrocket\AmpEmail\Api\AmpTemplateProviderInterface $ampTemplateProvider,
        array $data = []
    ) {
        parent::__construct($context, $resourceHelper, $data);
        $this->templateCollectionFactory = $templateCollectionFactory;
        $this->dataHelper = $dataHelper;
        $this->ampTemplateProvider = $ampTemplateProvider;
    }

    /**
     * @return array
     */
    protected function _getOptions() //@codingStandardsIgnoreLine
    {
        $result = [];

        foreach ($this->dataHelper->getAmpEmailTemplateStatuses(true) as $code => $label) {
            $result[] = ['value' => $code, 'label' => __($label)];
        }

        return $result;
    }

    /**
     * @return array|null
     */
    public function getCondition()
    {
        if (null === $this->getValue()) {
            return null;
        }

        /** @var \Magento\Email\Model\ResourceModel\Template\Collection $templateCollection */
        $templateCollection = $this->templateCollectionFactory->create();

        try {
            switch ($this->getValue()) {
                case AmpTemplateInterface::AMP_EMAIL_STATUS_LIVE:
                    $templateIds = $this->getLiveTemplateIds($templateCollection);
                    break;
                case AmpTemplateInterface::AMP_EMAIL_STATUS_SANDBOX:
                    $templateIds = $this->getSandboxTemplateIds($templateCollection);
                    break;
                default:
                    $templateIds = $this->getDisabledTemplateIds($templateCollection);
            }
        } catch (\Magento\Framework\Exception\NoSuchEntityException $noSuchEntityException) {
            $templateIds = [];
        }

        return ['in' => $templateIds];
    }

    /**
     * @param \Magento\Email\Model\ResourceModel\Template\Collection $templateCollection
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getLiveTemplateIds(
        \Magento\Email\Model\ResourceModel\Template\Collection $templateCollection
    ) : array {
        $templateIds = [];
        foreach ($templateCollection as $template) {
            if ($this->ampTemplateProvider->isExistAmpForEmailById($template->getId())) {
                $ampTemplate = $this->ampTemplateProvider->getTemplate($template->getId());
                if ($ampTemplate->isEnabledAmpForEmail() && $ampTemplate->isAmpForEmailInLive()) {
                    $templateIds[] = $template->getId();
                }
            }
        }
        return $templateIds;
    }

    /**
     * @param \Magento\Email\Model\ResourceModel\Template\Collection $templateCollection
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getSandboxTemplateIds(
        \Magento\Email\Model\ResourceModel\Template\Collection $templateCollection
    ) : array {
        $templateIds = [];
        foreach ($templateCollection as $template) {
            if ($this->ampTemplateProvider->isExistAmpForEmailById($template->getId())) {
                $ampTemplate = $this->ampTemplateProvider->getTemplate($template->getId());
                if ($ampTemplate->isEnabledAmpForEmail() && $ampTemplate->isAmpForEmailInSandbox()) {
                    $templateIds[] = $template->getId();
                }
            }
        }
        return $templateIds;
    }

    /**
     * @param \Magento\Email\Model\ResourceModel\Template\Collection $templateCollection
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getDisabledTemplateIds(
        \Magento\Email\Model\ResourceModel\Template\Collection $templateCollection
    ) : array {
        $templateIds = [];

        foreach ($templateCollection as $template) {
            if ($this->ampTemplateProvider->isExistAmpForEmailById($template->getId())) {
                $ampTemplate = $this->ampTemplateProvider->getTemplate($template->getId());
                if (! $ampTemplate->isEnabledAmpForEmail()) {
                    $templateIds[] = $template->getId();
                }
            } else {
                $templateIds[] = $template->getId();
            }
        }
        return $templateIds;
    }
}
