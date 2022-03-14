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

namespace Plumrocket\AmpEmail\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * Block Factory
     * @var \Magento\Cms\Model\BlockFactory
     */
    private $blockFactory;

    /**
     * UpgradeData constructor.
     *
     * @param \Magento\Cms\Model\BlockFactory $blockFactory
     * @param \Magento\Framework\App\State    $state
     */
    public function __construct(
        \Magento\Cms\Model\BlockFactory $blockFactory,
        \Magento\Framework\App\State $state
    ) {
        $this->blockFactory = $blockFactory;

        try {
            $state->setAreaCode('adminhtml');
        } catch (\Exception $e) {}
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            /**
             * Set block identifier and check it for storeID = 0
             * @var $block \Magento\Cms\Model\Block
             */
            $block = $this->blockFactory->create();
            $blockIdentifier = \Plumrocket\AmpEmail\Helper\Data::AMP_CMS_SOCIAL_BLOCK_IDENTIFIER;
            $block->setStoreId(0)->load($blockIdentifier);

            if (! $block->getId()) {
                /**
                 * Prepare data for AMP footer block and create it
                 */
                $blockContent = '<ul class="social">
    <li class="social__item">
        <a href="https://www.facebook.com/" aria-label="Facebook">
            <img src="{{view url=\'Plumrocket_AmpEmail/images/social/facebook_icon.png\'}}" width="33" height="33" alt="Facebook"/>
        </a>
    </li>
    <li class="social__item">
        <a href="https://twitter.com"  aria-label="Twitter">
            <img src="{{view url=\'Plumrocket_AmpEmail/images/social/twitter_icon.png\'}}" width="33" height="33" alt="Twitter"/>
        </a>
    </li>
    <li class="social__item">
        <a href="https://www.youtube.com/" aria-label="Youtube">
            <img src="{{view url=\'Plumrocket_AmpEmail/images/social/youtube_icon.png\'}}" width="33" height="33" alt="Youtube"/>
        </a>
    </li>
    <li class="social__item">
        <a href="https://www.instagram.com" aria-label="Instagram">
            <img src="{{view url=\'Plumrocket_AmpEmail/images/social/instagram_icon.png\'}}" width="33" height="33" alt="Instagram"/>
        </a>
    </li>
</ul>';

                $socialBlockData = [
                    \Magento\Cms\Model\Block::IDENTIFIER => $blockIdentifier,
                    \Magento\Cms\Model\Block::TITLE => 'Amp Email Social Buttons',
                    \Magento\Cms\Model\Block::CONTENT => $blockContent,
                    \Magento\Cms\Model\Block::IS_ACTIVE => true,
                    'page_layout' => '1column',
                    'stores' => [0],
                ];

                $block->setData($socialBlockData)->save();
            }
        }

        $setup->endSetup();
    }
}
