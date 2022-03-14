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

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Mirasvit\ProductAction\Api\ActionInterface;
use Mirasvit\ProductAction\Repository\ActionRepository;

class Execute extends Action
{
    private $filter;

    private $actionRepository;

    private $productCollectionFactory;

    public function __construct(
        ActionRepository $actionRepository,
        Filter $filter,
        ProductCollectionFactory $productCollectionFactory,
        Context $context
    ) {
        $this->actionRepository         = $actionRepository;
        $this->filter                   = $filter;
        $this->productCollectionFactory = $productCollectionFactory;

        parent::__construct($context);
    }

    public function execute()
    {
        $action     = $this->actionRepository->get($this->getRequest()->getParam(ActionInterface::CODE));
        $productIds = $this->getProductIds();

        try {
            $action->process($productIds, $this->getRequest()->getParams());

            $this->messageManager->addSuccessMessage($action->getMessage());
        } catch (LocalizedException $e) {
            $msg = $e->getMessage();
            $pe  = $e->getPrevious();
            if ($pe) {
                $msg .= ' ' . $pe->getMessage();
            }
            $this->messageManager->addErrorMessage($msg);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        $redirect = $this->resultRedirectFactory->create();

        return $redirect->setPath('catalog/product/index');
    }

    private function getProductIds(): array
    {
        /** @var \Magento\Framework\App\Request\Http $request */
        $request = $this->getRequest();

        foreach ($request->getParam('selection', []) as $key => $value) {
            $request->setParam($key, $value);
        }
        $request->setParam('selection', false);

        $collection = $this->productCollectionFactory->create();

        $collection = $this->filter->getCollection($collection);

        return $collection->getAllIds();
    }
}
