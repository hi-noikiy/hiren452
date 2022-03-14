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
namespace Magedelight\Facebook\Cron;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magedelight\Facebook\Helper\Data as FbHelper;
use Magedelight\Facebook\Model\CronhistoryFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magedelight\Facebook\Model\Cronhistory;
use Magedelight\Facebook\Logger\Logger;

class GenerateFeed
{
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
     * @param Filesystem $filesystem
     * @param DirectoryList $directorylist
     * @param FbHelper $dataHelper
     */
    public function __construct(
        Filesystem $filesystem,
        DirectoryList $directorylist,
        FbHelper $dataHelper,
        CronhistoryFactory $cronhistoryFactory,
        DateTime $date,
        Logger $logger     
    ) {
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->directorylist = $directorylist;
        $this->dataHelper = $dataHelper;
        $this->cronhistoryFactory = $cronhistoryFactory;
        $this->date = $date;
        $this->logger = $logger;
    }
    
    public function execute() {
        if($this->dataHelper->isEnabled() && $this->dataHelper->isCronEnabled()){
            $this->logger->info('Feed process Start');
            $storeId = 0;
            $name = 'fbshop';
            $filepath = 'fb' . DIRECTORY_SEPARATOR . $name . '.csv';
            $stream = $this->directory->openFile($filepath, 'w+');
            $stream->lock();
            $columns = $this->dataHelper->getDataHeader(Cronhistory::CRON);
            if($columns){
                foreach ($columns as $column) {
                    $header[] = $column;
                }
            }
            else{
                // log error
                return;
            }
            /* Write Header */
            $stream->writeCsv($header);
            $productdata = $this->dataHelper->getProductData($columns,$storeId,Cronhistory::CRON);
            if($productdata)
            {
                $size = 1;
                foreach ($productdata as $item) {
                    $stream->writeCsv($item);
                    $size++;
                }   
            }
            else{
                // log error
                return;
            }
            $cronHistoryModel = $this->cronhistoryFactory->create();
            $date = $this->date->gmtDate();
            $cronHistoryModel->setCronDate($date);
            $cronHistoryModel->setMessage(__("Feed Generated Successfully"));
            $cronHistoryModel->setType(Cronhistory::CRON);
            $cronHistoryModel->setStatus(Cronhistory::SUCCESS);
            $cronHistoryModel->save();
            $this->logger->info('Feed process End');
        }
    }
}

