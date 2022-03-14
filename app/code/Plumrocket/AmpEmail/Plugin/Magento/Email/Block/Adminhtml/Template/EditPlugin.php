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

namespace Plumrocket\AmpEmail\Plugin\Magento\Email\Block\Adminhtml\Template;

class EditPlugin
{
    /**
     * @param \Magento\Email\Block\Adminhtml\Template\Edit $form
     * @param                                              $alias
     * @param                                              $block
     * @param                                              $params
     * @return array
     */
    public function beforeAddChild( //@codingStandardsIgnoreLine
        \Magento\Email\Block\Adminhtml\Template\Edit $form,
        $alias,
        $block,
        $params = []
    ) {
        if ('form' === $alias
            && \Magento\Email\Block\Adminhtml\Template\Edit\Form::class === $block
        ) {
            $block = \Plumrocket\AmpEmail\Block\Adminhtml\Email\Template\Edit\Form::class;
        }

        return [$alias, $block, $params];
    }
}
