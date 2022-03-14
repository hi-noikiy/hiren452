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
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Filesystem;
use Magedelight\Facebook\Helper\Data as FbHelper;
use Magedelight\Facebook\Model\CronhistoryFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magedelight\Facebook\Model\Cronhistory;
use Magedelight\Facebook\Logger\Logger;

class GenerateFeed extends \Magento\Framework\App\Action\Action
{
    /**
     *
     * @var JsonFactory 
     */
    protected $resultJsonFactory;
    
    /**
     *
     * @var FileFactory 
     */
    protected $fileFactory;
    
    /**
     *
     * @var Filesystem 
     */
    protected $filesystem;
    
    /**
     *
     * @var DirectoryList 
     */
    protected $directorylist;
    
    /**
     *
     * @var FbHelper 
     */
    protected $dataHelper;
    
    /**
     *
     * @var CronhistoryFactory 
     */
    protected $cronhistoryFactory;
    
    /**
     *
     * @var DateTime 
     */
    protected $date;
    
    /**
     *
     * @var Logger 
     */
    protected $logger;

    /**
     * 
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\Filesystem $filesystem
     * @param DirectoryList $directorylist
     * @param \Magedelight\Facebook\Helper\Data $dataHelper
     */
    public function __construct(
        Context $context,
        FileFactory $fileFactory,
        Filesystem $filesystem,
        DirectoryList $directorylist,
        FbHelper $dataHelper,
        JsonFactory $resultJsonFactory,
        CronhistoryFactory $cronhistoryFactory,
        DateTime $date,
        Logger $logger    
    ) {
        $this->_fileFactory = $fileFactory;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->directorylist = $directorylist;
        $this->dataHelper = $dataHelper;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->cronhistoryFactory = $cronhistoryFactory;
        $this->date = $date;
        $this->logger = $logger;
        parent::__construct($context);
    }

    public function execute()
    {
        $this->logger->info('Feed process Start');
        $storeId = $this->getStoreParam();
        $name = 'fbshop';
        $filepath = 'fb' . DIRECTORY_SEPARATOR . $name . '.csv';
        $progressfilepath = 'fb' . DIRECTORY_SEPARATOR . 'progress.txt';
        $progressfile = $this->directorylist->getPath(DirectoryList::MEDIA).DIRECTORY_SEPARATOR.'fb'.DIRECTORY_SEPARATOR.'progress.txt';
        /* Open file */
        if (is_file($progressfile)) {
            unlink($progressfile);
        }
        $this->directory->openFile($progressfilepath, 'w+');
        $stringData = 0;
        file_put_contents($progressfile, $stringData);
        $stream = $this->directory->openFile($filepath, 'w+');
        $stream->lock();
        $columns = $this->dataHelper->getDataHeader(Cronhistory::MANUAL);
        if($columns){
            foreach ($columns as $column) {
                $header[] = $column;
            }
        }
        else{
            $message = $this->dataHelper->getErrorMsg();
            $this->messageManager->addError($message);
            $response['error'] = true;
            $resultJson = $this->resultJsonFactory->create();
            $resultJson->setData($response);
            return $resultJson;
        }
        /* Write Header */
        $stream->writeCsv($header);
        $productdata = $this->dataHelper->getProductData($columns,$storeId,Cronhistory::MANUAL);
        if($productdata)
        {
            $datasize = count($productdata);
        }
        else{
            $message = $this->dataHelper->getErrorMsg();
            $this->messageManager->addError($message);
            $response['error'] = true;
            $resultJson = $this->resultJsonFactory->create();
            $resultJson->setData($response);
            return $resultJson;
        }
        
        $size = 1;
        foreach ($productdata as $item) {
            $stringData = ($size * 100) / $datasize;
            $stream->writeCsv($item);
            $temdata = file_get_contents($progressfile);
            $this->logger->info('progress' . $temdata);
            file_put_contents($progressfile, $stringData);
            $size++;
        }
        
        $cronHistoryModel = $this->cronhistoryFactory->create();
        $date = $this->date->gmtDate();
        $cronHistoryModel->setCronDate($date);
        $cronHistoryModel->setMessage(__("Feed Generated Successfully"));
        $cronHistoryModel->setType(Cronhistory::MANUAL);
        $cronHistoryModel->setStatus(Cronhistory::SUCCESS);
        $cronHistoryModel->save();
        $response['error'] = false;
        $resultJson = $this->resultJsonFactory->create();
        $resultJson->setData($response);
        $this->logger->info('Feed process End');
        return $resultJson;
    } 

    public function getStoreParam()
    {
        return $this->getRequest()->getParam('store', 0);
    }
    
    protected function _isAllowed()
    {
        return true;
    }
}
