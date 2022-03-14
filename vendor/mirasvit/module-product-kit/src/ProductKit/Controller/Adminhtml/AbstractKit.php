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



namespace Mirasvit\ProductKit\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Mirasvit\ProductKit\Api\Data\KitInterface;
use Mirasvit\ProductKit\Controller\Adminhtml\Kit\PostDataProcessor;
use Mirasvit\ProductKit\Repository\KitRepository;

abstract class AbstractKit extends Action
{
    protected $kitRepository;

    protected $postDataProcessor;

    protected $registry;

    protected $context;

    public function __construct(
        KitRepository $kitRepository,
        PostDataProcessor $postDataProcessor,
        Registry $registry,
        Context $context
    ) {
        $this->kitRepository     = $kitRepository;
        $this->postDataProcessor = $postDataProcessor;
        $this->registry          = $registry;
        $this->context           = $context;

        parent::__construct($context);
    }

    /**
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function initPage($resultPage)
    {
        $resultPage->setActiveMenu('Magento_Catalog::catalog');
        $resultPage->getConfig()->getTitle()->prepend(__('Product Kits'));
        $resultPage->getConfig()->getTitle()->prepend(__('Kits'));

        return $resultPage;
    }

    /**
     * @return KitInterface
     */
    public function initModel()
    {
        $model = $this->kitRepository->create();

        if ($this->getRequest()->getParam(KitInterface::ID)) {
            $model = $this->kitRepository->get($this->getRequest()->getParam(KitInterface::ID));
        }
        $this->registry->register(KitInterface::class, $model);

        return $model;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->context->getAuthorization()->isAllowed('Mirasvit_ProductKit::kit');
    }
}
