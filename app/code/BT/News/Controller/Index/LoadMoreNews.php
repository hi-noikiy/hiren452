<?php
namespace Kitchen365\News\Controller\Index;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;

class LoadMoreNews extends \Magento\Framework\App\Action\Action implements HttpPostActionInterface
{
    /**
     * Index action
     *
     * @return void
     */
    protected $_pageFactory;

    /**
     * @var Session
     */
    protected $_resultJsonFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\Result\PageFactory $pageFactory
    ) {
        $this->_pageFactory = $pageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        return parent::__construct($context);
    }
    public function execute()
    {
        $resultPage = $this->_pageFactory->create();
        $pageNo = $this->getRequest()->getPost('pageno');
        $result = $this->resultJsonFactory->create();
        $block = $resultPage->getLayout()
            ->createBlock('Kitchen365\News\Block\News')
            ->setTemplate('Kitchen365_News::newsappend.phtml')
            ->setData('data', $pageNo)
            ->toHtml();

        $result->setData(['output' => $block]);
        return $result;
    }
}
