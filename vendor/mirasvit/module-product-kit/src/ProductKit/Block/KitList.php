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



namespace Mirasvit\ProductKit\Block;


use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use Mirasvit\ProductKit\Data\OfferKit;
use Mirasvit\ProductKit\Model\Config\BlockPositionSource;
use Mirasvit\ProductKit\Model\ConfigProvider;
use Mirasvit\ProductKit\Service\OfferKitService;

class KitList extends Template implements BlockInterface
{
    private $blockPositionSource;

    private $kitService;

    private $configProvider;

    private $customerSession;

    private $context;

    private $kitTemplate = 'Mirasvit_ProductKit::kit.phtml';

    protected $_template = 'Mirasvit_ProductKit::kitList.phtml';

    public function __construct(
        BlockPositionSource $blockPositionSource,
        OfferKitService $kitService,
        ConfigProvider $configProvider,
        CustomerSession $customerSession,
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->blockPositionSource = $blockPositionSource;
        $this->kitService          = $kitService;
        $this->configProvider      = $configProvider;
        $this->customerSession     = $customerSession;
        $this->context             = $context;
    }

    /**
     * @param string $template
     */
    public function setKitTemplate($template)
    {
        $this->kitTemplate = $template;
    }

    public function getOfferKits()
    {
        $product = $this->kitService->getContextProduct();

        if (!$product) {
            return [];
        }

        $storeId = $this->context->getStoreManager()->getStore()->getId();
        $groupId = $this->customerSession->getCustomerGroupId();

        return $this->kitService->findSuitableKits($product->getId(), $groupId, $storeId);
    }

    public function getKitHtml(OfferKit $offerKit)
    {
        /** @var Kit $block */
        $block = $this->_layout->createBlock(Kit::class);

        $block->setOfferKit($offerKit)
            ->setTemplate($this->kitTemplate);

        return $block->toHtml();
    }

    public function getJsonConfig()
    {
        return [
            '[data-element=kitList]' => [
                'Mirasvit_ProductKit/js/kit-list' => [],
            ],
        ];
    }

    public function toHtml()
    {
        $product = $this->kitService->getContextProduct();

        if (
            !$product || (
                in_array($this->getRequest()->getFullActionName(), $this->blockPositionSource->getOptions()) &&
                !in_array($this->getRequest()->getFullActionName(), $this->configProvider->getDisplayOn())
            )
        ) {
            return '';
        }

        return parent::toHtml();
    }
}
