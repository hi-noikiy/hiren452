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
 * @package   mirasvit/module-product-action
 * @version   1.0.9
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);

namespace Mirasvit\ProductAction\Controller\Adminhtml\Action;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Element\UiComponent\ContextFactory;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Controller\Adminhtml\AbstractAction;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AjaxLoad extends AbstractAction implements HttpGetActionInterface
{
    private $contextFactory;

    public function __construct(
        Context $context,
        UiComponentFactory $factory,
        ContextFactory $contextFactory = null
    ) {
        parent::__construct($context, $factory);

        $this->contextFactory = $contextFactory ? : ObjectManager::getInstance()->get(ContextFactory::class);
    }

    public function execute()
    {
        $namespace = $this->_request->getParam('namespace');
        $code      = $this->_request->getParam('code');

        $this->_view->loadLayout(['default'], true, true, false);

        $layout = $this->_view->getLayout();

        $this->contextFactory->create([
            'namespace'  => $namespace,
            'pageLayout' => $layout,
        ]);

        $block = $layout->createBlock(
            \Mirasvit\ProductAction\Ui\Action\Block\ActionBlock::class,
            '',
            ['data' => ['code' => $code]]
        );

        $this->_response->appendBody($block->toHtml());
    }
}
