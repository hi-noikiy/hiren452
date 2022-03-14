<?php
/**
 * @category  Magento OrderDelete
 * @package   Mageants_OrderDelete
 * @copyright Copyright (c) 2017 Magento
 * @author    Mageants Team <support@mageants.com>
 */

namespace Mageants\OrderDelete\Controller\Adminhtml\Sales;

use Magento\Backend\App\Action;

/**
 * Delete class
 */
class Delete extends \Magento\Sales\Controller\Adminhtml\Order
{
    /**
     * Authorization level of a basic admin session
     *
     */
    const ADMIN_RESOURCE = 'Magento_Sales::actions_delete';

    /**
     * Delete order action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
		
        $order = $this->_initOrder();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($order) {
            try {
                $order->delete();
                $this->messageManager->addSuccess(__('You deleted the item.'));
                $resultRedirect->setPath('sales/order/index');
                return $resultRedirect;
            } catch (\Exception $e) {
                $this->logger->critical($e);
                $this->messageManager->addError(__('Exception occurred during order load'));
                $resultRedirect->setPath('sales/order/index');
                return $resultRedirect;
            }
        }
        $resultRedirect->setPath('sales/*/');
        return $resultRedirect;
    }
}
