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

namespace Plumrocket\AmpEmail\Block\Adminhtml\Template\Grid\Renderer;

/**
 * Class Type
 *
 * @package Plumrocket\AmpEmail\Block\Adminhtml\Template\Grid\Renderer
 */
class Type extends \Magento\Email\Block\Adminhtml\Template\Grid\Renderer\Type
{
    /**
     * @var \Plumrocket\AmpEmail\Api\AmpTemplateProviderInterface
     */
    private $ampTemplateProvider;

    /**
     * Type constructor.
     *
     * @param \Magento\Backend\Block\Context                        $context
     * @param \Plumrocket\AmpEmail\Api\AmpTemplateProviderInterface $ampTemplateProvider
     * @param array                                                 $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Plumrocket\AmpEmail\Api\AmpTemplateProviderInterface $ampTemplateProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->ampTemplateProvider = $ampTemplateProvider;
    }

    /**
     * @param \Magento\Framework\DataObject $row
     * @return \Magento\Framework\Phrase
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $currentType = parent::$_types[$row->getTemplateType()] ?? __('Unknown');

        if ($this->ampTemplateProvider->isExistAmpForEmailById($row->getId())) {
            $currentType = __($currentType) . ', ' . $this->wrapType('AMP');
        }

        return $currentType;
    }

    /**
     * @param string $type
     * @return string
     */
    private function wrapType(string $type) : string
    {
        return '<span style="color:#ffd643;">' . '⚡' . '</span> <span style="color:#45a8ff;">' . __($type) . '</span>';
    }
}
