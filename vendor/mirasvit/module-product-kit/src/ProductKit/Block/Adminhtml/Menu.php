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



namespace Mirasvit\ProductKit\Block\Adminhtml;

use Magento\Backend\Block\Template\Context;
use Mirasvit\Core\Block\Adminhtml\AbstractMenu;

class Menu extends AbstractMenu
{

    public function __construct(
        Context $context
    ) {
        $this->visibleAt(['product_kit']);

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function buildMenu()
    {
        $this->addItem([
            'resource' => 'Mirasvit_ProductKit::kit_management',
            'title'    => __('Manage Kits'),
            'url'      => $this->urlBuilder->getUrl('product_kit/kit'),
        ])->addItem([
            'resource' => 'Mirasvit_ProductKit::kit_suggester',
            'title'    => __('Suggest Kits'),
            'url'      => $this->urlBuilder->getUrl('product_kit/suggester'),
        ]);

        $this->addSeparator();

        $this->addItem([
            'resource' => 'Mirasvit_ProductKit::config',
            'title'    => __('Configuration'),
            'url'      => $this->urlBuilder->getUrl('adminhtml/system_config/edit/section/product_kit'),
        ]);


        return $this;
    }
}
