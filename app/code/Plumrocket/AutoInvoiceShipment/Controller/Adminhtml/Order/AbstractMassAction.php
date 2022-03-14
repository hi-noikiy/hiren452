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
 * @package   Plumrocket_AutoInvoiceShipment
 * @copyright Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license   http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\AutoInvoiceShipment\Controller\Adminhtml\Order;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Ui\Component\MassAction\Filter;
use Plumrocket\AutoInvoiceShipment\Model\AutoAbstract;

abstract class AbstractMassAction extends Action
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * @var AutoAbstract
     */
    protected $model;

    /**
     * @param int $successOrderCount
     * @return string
     */
    abstract protected function getSuccessMessage(int $successOrderCount);

    /**
     * @param int $errorOrderCount
     * @return string
     */
    abstract public function getErrorMessage(int $errorOrderCount);

    /**
     * MassInvoice constructor.
     *
     * @param Filter $filter
     * @param CollectionFactory $orderCollectionFactory
     * @param AutoAbstract $model
     * @param Action\Context $context
     */
    public function __construct(
        Filter $filter,
        CollectionFactory $orderCollectionFactory,
        AutoAbstract $model,
        Action\Context $context
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->model = $model;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        try {
            $countSuccessOrders = 0;
            $collection = $this->filter->getCollection($this->orderCollectionFactory->create());

            foreach ($collection->getItems() as $order) {
                $isCreated = $this->model->make($order);
                if (false === $isCreated) {
                    continue;
                }
                $countSuccessOrders++;
            }

            $countErrorOrders = count($collection->getItems()) - $countSuccessOrders;

            if ($countSuccessOrders) {
                $this->messageManager->addSuccessMessage(
                    $this->getSuccessMessage($countSuccessOrders)
                );
            }

            if ($countErrorOrders) {
                $this->messageManager->addErrorMessage(
                    $this->getErrorMessage($countErrorOrders)
                );
            }

            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath($this->filter->getComponentRefererUrl() ?: 'sales/*/');

            return $resultRedirect;
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            return $resultRedirect->setPath('*/*/');
        }
    }
}
