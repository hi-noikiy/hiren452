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

class Index extends \FME\Mediaappearance\Controller\Adminhtml\Mediaappearance
{
    
    public function execute()
    {
        
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('FME_Mediaappearance::media_page');
        $resultPage->addBreadcrumb(__('Mediaappearance'), __('Media Gallery'));
        $resultPage->addBreadcrumb(__('Manage Media'), __('Manage Media'));
        $resultPage->getConfig()->getTitle()->prepend(__('Media Gallery'));
        return $resultPage;
    }
}
