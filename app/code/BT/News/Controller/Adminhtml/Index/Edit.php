<?php

namespace BT\News\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

class Edit extends Action implements HttpGetActionInterface
{
    protected $_coreRegistry = null;
    protected $resultPageFactory;
    protected $newsFactory;
    protected $_session;

    /**
     * Edit constructor.
     * @param Action\Context $context
     * @param PageFactory $resultPageFactory
     * @param Registry $registry
     */
    public function __construct(
        Action\Context $context,
        \BT\News\Model\NewsFactory $newsFactory,
        \Magento\Backend\Model\Session $session,
        PageFactory $resultPageFactory,
        Registry $registry
    ) {
        $this->_session = $session;
        $this->_coreRegistry = $registry;
        $this->newsFactory = $newsFactory;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Check the permission
     *
     * @return bool
     */
    //protected function _isAllowed()
    //{
    //    return $this->_authorization->isAllowed(self::ADMIN_RESOURCE);
    //}

    /**
     * Init action
     *
     * @return \Magento\Framework\View\Result\Page
     */
    protected function _initAction()
    {
        $resultPage = $this->resultPageFactory->create();
        return $resultPage;
    }

    /**
     * Edit / update contact us data from admin
     *
     * @return $this|\Magento\Backend\Model\View\Result\Page|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->newsFactory->create();
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This News no longer exists.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }
        $data = $this->_session->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        //$model->save();
        $this->_coreRegistry->register('news', $model);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend(__('Aumika News Form'));

        return $resultPage;
    }
}
