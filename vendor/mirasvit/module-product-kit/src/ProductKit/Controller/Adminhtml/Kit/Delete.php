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



namespace Mirasvit\ProductKit\Controller\Adminhtml\Kit;

use Mirasvit\ProductKit\Api\Data\KitInterface;
use Mirasvit\ProductKit\Controller\Adminhtml\AbstractKit;

class Delete extends AbstractKit
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $model = $this->initModel();

        $resultRedirect = $this->resultRedirectFactory->create();

        if ($model->getId()) {
            try {
                $this->kitRepository->delete($model);

                $this->messageManager->addSuccessMessage(__('The product kit has been deleted.'));

                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());

                return $resultRedirect->setPath('*/*/edit', [KitInterface::ID => $model->getId()]);
            }
        } else {
            $this->messageManager->addErrorMessage(__('This product kit no longer exists.'));

            return $resultRedirect->setPath('*/*/');
        }
    }
}
