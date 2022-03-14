<?php

namespace BT\News\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use Magento\Framework\Exception\LocalizedException;

class Delete extends \Magento\Backend\App\Action implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'BT_News::event_delete';

    protected $newsFactory;

    /**
     * Delete constructor.
     * @param Action\Context $context
     */
    public function __construct(
        Action\Context $context,
        \BT\News\Model\NewsFactory $newsFactory
    ) {
        $this->newsFactory = $newsFactory;
        parent::__construct($context);
    }

    /**
     * Check the permission
     *
     * @return bool
     */
    public function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::ADMIN_RESOURCE);
    }

    /**
     * Delete contact us record by id
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $model = $this->newsFactory->create();
                $model->load($id);
                $model->delete();
                $this->_redirect('news/*/');
                $this->messageManager->addSuccess(__('Deleted News Record successfully.'));
                return;
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('We can\'t delete this News right now. Please review the log and try again.')
                );
                $this->_redirect('news/*/edit', ['id' => $this->getRequest()->getParam('id')]);
                return;
            }
        }
        $this->messageManager->addError(__('We can\'t find a rule to delete.'));
        $this->_redirect('news/*/');
    }
}
