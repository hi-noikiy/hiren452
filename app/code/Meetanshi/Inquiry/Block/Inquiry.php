<?php

namespace Meetanshi\Inquiry\Block;

use Magento\Framework\View\Element\Template;
use Magento\Directory\Model\ResourceModel\Country\CollectionFactory;
use Magento\Framework\View\Element\Template\Context;
use Meetanshi\Inquiry\Helper\Data as Helper;
use Magento\Store\Model\ScopeInterface;
use Magento\Directory\Block\Data;

class Inquiry extends Template
{
    protected $countryCollectionFactory;
    protected $directoryBlock;
    protected $helper;

    public function __construct(
        CollectionFactory $countryCollectionFactory,
        Data $directoryBlock,
        Context $context,
        Helper $helper
    )
    {

        parent::__construct($context);
        $this->countryCollectionFactory = $countryCollectionFactory;
        $this->directoryBlock = $directoryBlock;
        $this->helper = $helper;
    }

    public function getHelper()
    {
        return $this->helper;
    }

    public function getRegion()
    {
        $region = $this->directoryBlock->getRegionHtmlSelect();
        return $region;
    }

    public function getCountryAction()
    {
        return $this->getUrl('inquiry/index/country', ['_secure' => true]);
    }

    public function getCountryCollection()
    {
        $collection = $this->countryCollectionFactory->create()->loadByStore();
        return $collection;
    }

    protected function getTopDestinations()
    {
        $destinations = (string)$this->_scopeConfig->getValue(
            'general/country/destinations',
            ScopeInterface::SCOPE_STORE
        );
        return !empty($destinations) ? explode(',', $destinations) : [];
    }

    public function getCountries()
    {
        $options = $this->getCountryCollection()
            ->setForegroundCountries($this->getTopDestinations())
            ->toOptionArray();
        $key = array('label');
        $options = $this->sortCountries($options, $key);
        return $options;
    }

    public function getFormAction()
    {
        return $this->getUrl() . 'inquiry/index/post';
    }

    public function sortCountries($array, $key, $sort_flags = SORT_REGULAR)
    {
        if (is_array($array) && !empty($array)) {
            if (!empty($key)) {
                $mapping = [];
                foreach ($array as $k => $v) {
                    $sort_key = '';
                    if (!is_array($key)) {
                        $sort_key = $v[$key];
                    } else {
                        foreach ($key as $key_key) {
                            $sort_key .= $v[$key_key];
                        }
                        $sort_flags = SORT_STRING;
                    }
                    $mapping[$k] = $sort_key;
                }
                asort($mapping, $sort_flags);
                $sorted = [];
                foreach ($mapping as $k => $v) {
                    $sorted[] = $array[$k];
                }
                return $sorted;
            }
        }
        return $array;
    }
}
