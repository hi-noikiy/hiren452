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

namespace Plumrocket\AmpEmail\Model\Component\Parts\State;

use Plumrocket\AmpEmail\Model\Component\Parts\StateCompositeRenderStrategy;

class Cart implements \Plumrocket\AmpEmail\Model\ComponentPartRenderStrategyInterface
{
    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    private $layout;

    /**
     * Cart constructor.
     *
     * @param \Magento\Framework\View\LayoutInterface $layout
     */
    public function __construct(
        \Magento\Framework\View\LayoutInterface $layout
    ) {
        $this->layout = $layout;
    }

    /**
     * @param array  $partContents
     * @param string $emailContent
     * @return string
     */
    public function render(array $partContents, string $emailContent) : string
    {
        $idPrefix = 'p';

        $blockData = [
            'template' => 'component/v1/state/cart.phtml',
            'state_id' => 'cart',
            'id_prefix' => $idPrefix,
        ];

        $cartStateBlock = $this->layout->createBlock(
            \Plumrocket\AmpEmail\Block\Component\State\Cart::class,
            null,
            ['data' => $blockData]
        );

        return str_replace(
            StateCompositeRenderStrategy::STATE_PART_PLACEHOLDER,
            StateCompositeRenderStrategy::STATE_PART_PLACEHOLDER . "\n" .$cartStateBlock->toHtml(),
            $emailContent
        );
    }
}
