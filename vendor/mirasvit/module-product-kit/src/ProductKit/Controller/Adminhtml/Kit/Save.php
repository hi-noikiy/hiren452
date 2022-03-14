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

class Save extends AbstractKit
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam(KitInterface::ID);

        $resultRedirect = $this->resultRedirectFactory->create();

        $data = $this->getRequest()->getParams();

        if ($data) {
            $model = $this->initModel();

            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This kit no longer exists.'));

                return $resultRedirect->setPath('*/*/');
            }

            $data = $this->postDataProcessor->filterPostData($data);

            $this->postDataProcessor->setData($model, $data);

            try {
                $this->kitRepository->save($model);

                $kitItems = $this->postDataProcessor->getKitItems($model, $data);
                $this->kitRepository->saveItems($model, $kitItems);

                $this->messageManager->addSuccessMessage(__('You have saved the kit.'));

                if ($model->isSmart()) {
                    $this->messageManager->addNoticeMessage(__('The changes will be applied only after Reindex.'));
                }

                if ($this->getRequest()->getParam('back') == 'edit') {
                    return $resultRedirect->setPath('*/*/edit', [KitInterface::ID => $model->getId()]);
                }

                return $this->context->getResultRedirectFactory()->create()->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());

                return $resultRedirect->setPath(
                    '*/*/edit',
                    [KitInterface::ID => $this->getRequest()->getParam(KitInterface::ID)]
                );
            }
        } else {
            $resultRedirect->setPath('*/*/');
            $this->messageManager->addErrorMessage('No data to save.');

            return $resultRedirect;
        }
    }
}
