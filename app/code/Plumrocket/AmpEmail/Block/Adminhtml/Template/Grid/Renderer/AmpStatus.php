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

use Plumrocket\AmpEmail\Model\Email\AmpTemplateInterface;

/**
 * Class AmpEnabled
 *
 * @package Plumrocket\AmpEmail\Block\Adminhtml\Template\Grid\Renderer
 */
class AmpStatus extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Plumrocket\AmpEmail\Helper\Data
     */
    private $dataHelper;

    /**
     * AmpStatus constructor.
     *
     * @param \Plumrocket\AmpEmail\Helper\Data $dataHelper
     * @param \Magento\Backend\Block\Context   $context
     * @param array                            $data
     */
    public function __construct(
        \Plumrocket\AmpEmail\Helper\Data $dataHelper,
        \Magento\Backend\Block\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->dataHelper = $dataHelper;
    }

    /**
     * @param \Magento\Framework\DataObject $row
     * @return \Magento\Framework\Phrase|string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        if ($row->getPrampEmailEnable() && $row->getPrampEmailContent()) {
            $status = $row->getPrampEmailMode();
        } else {
            $status = AmpTemplateInterface::AMP_EMAIL_STATUS_DISABLED;
        }

        $allStatuses = $this->dataHelper->getAmpEmailTemplateStatuses();

        if (isset($allStatuses[$status])) {
            $str = $this->htmlWrap($allStatuses[$status], $status);
        }

        return $str ?? __('Unknown');
    }

    /**
     * @param $statusTitle
     * @param $statusValue
     * @return string
     */
    private function htmlWrap(string $statusTitle, string $statusValue) : string
    {
        if (AmpTemplateInterface::AMP_EMAIL_STATUS_DISABLED !== $statusValue) {
            return '<span style="color: green;">' . __($statusTitle) . '</span>';
        }

        return __($statusTitle)->render();
    }
}
