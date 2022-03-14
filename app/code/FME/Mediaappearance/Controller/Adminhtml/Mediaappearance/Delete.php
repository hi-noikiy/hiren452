<?php

/**
 * FME Extensions
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the fmeextensions.com license that is
 * available through the world-wide-web at this URL:
 * https://www.fmeextensions.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  FME
 * @author     Atta <support@fmeextensions.com>
 * @package   FME_Mediaappearance
 * @copyright Copyright (c) 2019 FME (http://fmeextensions.com/)
 * @license   https://fmeextensions.com/LICENSE.txt
 */
namespace FME\Mediaappearance\Controller\Adminhtml\Mediaappearance;

use Magento\Framework\App\Filesystem\DirectoryList;

class Delete extends \FME\Mediaappearance\Controller\Adminhtml\Mediaappearance
{

    public function execute()
    {
        $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
                ->getDirectoryRead(DirectoryList::MEDIA);
        $mediaRootDir = $mediaDirectory->getAbsolutePath();
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = $this->_objectManager->create('FME\Mediaappearance\Model\Mediaappearance');
                $model->load($this->getRequest()->getParam('id'));

                /* Delete Media */
                $media_file = $model->getFilename();
                $media_thumb = $model->getFilethumb();

                if ($media_file != null) {
                    unlink($mediaRootDir . $media_file);
                }

                if ($media_thumb != null) {
                    unlink($mediaRootDir . $media_thumb);
                }

                $model->delete();

                $this->messageManager->addSuccess(__('Gallery was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
            }
        }
        $this->_redirect('*/*/');
    }
}
