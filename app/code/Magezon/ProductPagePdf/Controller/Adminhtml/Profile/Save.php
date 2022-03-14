<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://www.magezon.com/license
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_ProductPagePdf
 * @copyright Copyright (C) 2020 Magezon (https://www.magezon.com)
 */

namespace Magezon\ProductPagePdf\Controller\Adminhtml\Profile;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magezon\ProductPagePdf\Model\Processor;

class Save extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magezon_ProductPagePdf::profile_save';

    /**
     * @var \Magento\Framework\Stdlib\DateTime\Filter\Date
     */
    protected $dateFilter;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var Processor
     */
    protected $processor;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     * @param \Magezon\ProductPagePdf\Model\Processor $processor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Magezon\ProductPagePdf\Model\Processor $processor
    ) {
        parent::__construct($context);
        $this->dateFilter = $dateFilter;
        $this->dataPersistor = $dataPersistor;
        $this->processor = $processor;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        $redirectBack = $this->getRequest()->getParam('back', false);
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if (empty($data['profile_id'])) {
            unset($data['profile_id']);
        }
        
        if ($data) {
            /** @var \Magezon\ProductPagePdf\Model\Profile $model */
            $model = $this->_objectManager->create(\Magezon\ProductPagePdf\Model\Profile::class);
            $id    = $this->getRequest()->getParam('profile_id');
            if (isset($data['rule'])) {
                $data['conditions'] = $data['rule']['conditions'];
                unset($data['rule']);
            }

            unset($data['conditions_serialized']);
            unset($data['actions_serialized']);

            try {

                $filterValues = [];
                if ($data['from_date']) {
                    $filterValues['from_date'] = $this->dateFilter;
                }
                if ($this->getRequest()->getParam('to_date')) {
                    $filterValues['to_date'] = $this->dateFilter;
                }
                if ($filterValues) {
                    $inputFilter = new \Zend_Filter_Input(
                        $filterValues,
                        [],
                        $data
                    );
                    $data = $inputFilter->getUnescaped();
                } else {
                    $data['from_date'] = $data['to_date'] = null;
                }
                if ($data['product_types']) {
                    $data['product_types'] = json_encode($data['product_types']);
                }

                $model->loadPost($data);

                $model->load($id);
                if ($id && !$model->getId()) {
                    throw new LocalizedException(__('This profile no longer exists.'));
                }
                $model->setData($data);
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the profile.'));
                $this->dataPersistor->clear('current_profile');

                if ($this->getRequest()->getParam('auto_apply')) {
                    $this->processor->process($model);
                }

                if ($redirectBack === 'save_and_new') {
                    return $resultRedirect->setPath('*/*/new');
                }

                if ($redirectBack === 'save_and_duplicate') {
                    $duplicate = $this->_objectManager->create(\Magezon\ProductPagePdf\Model\Profile::class);
                    $duplicate->setData($model->getData());
                    $duplicate->setCreatedAt(null);
                    $duplicate->setUpdatedAt(null);
                    $duplicate->setId(null);
                    $duplicate->save();
                    $this->messageManager->addSuccessMessage(__('You duplicated the profile'));
                    return $resultRedirect->setPath('*/*/edit', ['profile_id' => $duplicate->getId(), '_current' => true]);
                }

                if ($redirectBack === 'save_and_close') {
                    return $resultRedirect->setPath('*/*/*');
                }

                return $resultRedirect->setPath('*/*/edit', ['profile_id' => $model->getId(), '_current' => true]);
            } catch (LocalizedException $e) {
                $this->messageManager->addExceptionMessage($e->getPrevious() ?:$e);
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the profile.'));
            }
            $this->dataPersistor->set('current_profile', $data);
            return $resultRedirect->setPath('*/*/edit', ['profile_id' => $this->getRequest()->getParam('profile_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
