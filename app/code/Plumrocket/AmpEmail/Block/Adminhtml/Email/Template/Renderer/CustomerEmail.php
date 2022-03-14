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

namespace Plumrocket\AmpEmail\Block\Adminhtml\Email\Template\Renderer;

/**
 * @method setElementParams(array $array)
 */
class CustomerEmail extends \Magento\Backend\Block\Widget\Form\Renderer\Fieldset\Element
{
    /**
     * @var string
     */
    protected $_template = 'Plumrocket_AmpEmail::widget/form/renderer/customer_email.phtml'; //@codingStandardsIgnoreLine

    /**
     * @param $paramName
     * @return mixed
     */
    public function getElementParams(string $paramName) : string
    {
        return (string) $this->getData('element_params/' . $paramName);
    }
}
