<?php
/**
 * Magedelight
 * Copyright (C) 2019 Magedelight <info@magedelight.com>
 *
 * @category Magedelight
 * @package Magedelight_Facebook
 * @copyright Copyright (c) 2019 Mage Delight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 */
namespace Magedelight\Facebook\Controller\Adminhtml\Cronhistory;

//use Magento\Framework\App\Filesystem\DirectoryList;

class Index extends \Magento\Backend\App\Action
{
    /**
     * 
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context
       ) {
        parent::__construct($context);
    }
    public function _initAction()
    {
       $this->_view->loadLayout();
       return $this;
    }

    public function execute()
    {
       $this->_initAction()->_setActiveMenu(
            'Magedelight_Facebook::facebook'
        )->_addBreadcrumb(
            __('Feed Action History'),
            __('Feed Action History')
        );
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Feed Action History'));
        $this->_view->renderLayout();
    }
    
    protected function _isAllowed()
    {
        return true;
    }
}
