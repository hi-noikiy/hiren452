<?php

namespace Meetanshi\Inquiry\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Directory\Model\RegionFactory;

class Country extends Action
{
    protected $resultJsonFactory;

    protected $regionColFactory;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        RegionFactory $regionColFactory
    )
    {
        $this->regionColFactory = $regionColFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();

        $result = $this->resultJsonFactory->create();
        $regions = $this->regionColFactory->create()->getCollection()->addFieldToFilter('country_id', $this->getRequest()->getParam('country'));

        $html = '';

        if ($regions->count() > 0) {
            $html .= '<option selected="selected" value="">Please select a region, state or province.</option>';
            foreach ($regions as $state) {
                $html .= '<option  value="' . $state->getName() . '">' . $state->getName() . '</option>';
            }
        }
        return $result->setData(['success' => true, 'value' => $html]);
    }
}
