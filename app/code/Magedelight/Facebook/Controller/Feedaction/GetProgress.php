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
namespace Magedelight\Facebook\Controller\Feedaction;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

class GetProgress extends Action
{
    protected $storeManager;
    protected $sessionStorage;
    public function __construct(
            Context $context,
            \Magento\Store\Model\StoreManager $storeManager,
            DirectoryList $directorylist,
            \Magento\Framework\Session\Storage $sessionStorage)
    {
        $this->storeManager = $storeManager;
        $this->sessionStorage = $sessionStorage;
        $this->directorylist = $directorylist;
        parent::__construct($context);
    }

    public function execute()
    {
        $myFile = $this->directorylist->getPath(DirectoryList::MEDIA).DIRECTORY_SEPARATOR.'fb'.DIRECTORY_SEPARATOR.'progress.txt';
        $data = 0;
        if (is_file($myFile)) {
            $fh = fopen($myFile, 'r');
            $data = fread($fh, filesize($myFile));
            fclose($fh);
        }
        if ($data == '100') {
            unlink($myFile);
            $text = __("Feed Generated Successfully.");
            $this->getResponse()->setHeader('Content-type', 'text/html');
            $this->getResponse()->setBody($text);
            $this->getResponse()->sendResponse();
        } 
        $this->getResponse()->setHeader('Content-type', 'text/html');
        $this->getResponse()->setBody($data);
        $this->getResponse()->sendResponse();
    }

    
    protected function _isAllowed()
    {
        return true;
    }
}
