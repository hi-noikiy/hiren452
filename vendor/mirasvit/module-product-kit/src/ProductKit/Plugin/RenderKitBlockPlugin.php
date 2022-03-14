<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-product-kit
 * @version   1.0.29
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\ProductKit\Plugin;

use Magento\Framework\View\Layout;
use Mirasvit\ProductKit\Model\ConfigProvider;

/**
 * @see \Magento\Framework\View\Layout::renderNonCachedElement
 */
class RenderKitBlockPlugin
{
    private $configProvider;

    private $blockName = [];

    public function __construct(
        ConfigProvider $configProvider
    ) {
        $this->configProvider = $configProvider;
    }

    /**
     * @param Layout $layout
     * @param string $name
     *
     * @return string
     */
    public function beforeRenderNonCachedElement($layout, $name)
    {
        $block = $layout->getBlock($name);
        if ($block) {
            array_push($this->blockName, $block->getNameInLayout());
        } else {
            array_push($this->blockName, $name);
        }

        return null;
    }

    /**
     * @param Layout $layout
     * @param string $result
     *
     * @return string
     */
    public function afterRenderNonCachedElement($layout, $result)
    {
        $bindBlock    = $this->configProvider->getLayoutRelativeContainer();
        $bindPosition = $this->configProvider->getLayoutRelativePosition();

        $blockName = array_pop($this->blockName);
        if ($blockName == $bindBlock && !$layout->hasElement('mst-kit.kit-list')) {
            /** @var \Mirasvit\ProductKit\Block\KitList $kitBlock */
            $kitBlock = $layout->createBlock('Mirasvit\ProductKit\Block\KitList', 'mst-kit.kit-list');
            $kitBlock->setTemplate('Mirasvit_ProductKit::kitList.phtml');
            $kitBlock->setKitTemplate('Mirasvit_ProductKit::kit.phtml');

            if ($bindPosition == 'before') {
                $result = $kitBlock->toHtml() . $result;
            } else {
                $result = $result . $kitBlock->toHtml();
            }
        }

        return $result;
    }
}
