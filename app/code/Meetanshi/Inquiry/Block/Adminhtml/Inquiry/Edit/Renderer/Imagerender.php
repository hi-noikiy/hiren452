<?php

namespace Meetanshi\Inquiry\Block\Adminhtml\Inquiry\Edit\Renderer;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Data\Form\Element\CollectionFactory;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Escaper;
use Meetanshi\Inquiry\Model\InquiryFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\Exception\LocalizedException;

class Imagerender extends AbstractElement
{
    protected $request;
    protected $inquiryFactory;
    protected $storeManager;

    public function __construct(
        Factory $factoryElement,
        CollectionFactory $factoryCollection,
        Escaper $escaper,
        Http $request,
        InquiryFactory $inquiryFactory,
        StoreManagerInterface $storeManager,
        array $data = []
    )
    {

        $this->request = $request;
        $this->inquiryFactory = $inquiryFactory;
        $this->storeManager = $storeManager;
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
    }

    public function getElementHtml()
    {
        try {
            $model = $this->inquiryFactory->create();
            $collection = $model->getCollection()
                ->addFieldToFilter('dealer_id', $this->request->getParam('id'));

            $custom = "No Images";

            if (!empty($collection->getData())) {
                $img_name = $collection->getData()[0]['files'];
                if (!empty($img_name)) {
                    $custom = '';
                    $imgs = explode(",", $img_name);
                    $alreadyDefine = null;
                    $bindHtml = null;
                    $currentStore = $this->storeManager->getStore();
                    $path = $mediaUrl = $currentStore->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . 'inquiry/';

                    if (sizeof($imgs) > 1) {
                        foreach ($imgs as $img) {
                            if (!empty($img)) {
                                $custom .= '<img src="' . $path . $img . '" width="95px" height="55px" alt="image" style="margin-right:10px"/>';
                            }
                        }
                    } else if (!empty($imgs[0])) {
                        $path = $mediaUrl = $currentStore->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
                        $custom = '<img src="' . $path . $imgs[0] . '" width="100px" height="55px" alt="image"/>';
                    }
                }
            }
            $customDiv = '<div>' . $custom . '</div>';
            return $customDiv;
        } catch (\Exception $e) {
            throw new LocalizedException($e->getMessage());
        }
    }
}
