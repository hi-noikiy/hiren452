<?php
namespace Mageants\OrderDelete\Plugin\Block\Adminhtml\Order;

class View
{
	protected  $HelperBackend;
    public function __construct(
	\Magento\Backend\Helper\Data $HelperBackend,
	\Magento\Backend\Model\UrlInterface $backendUrl
    ) {
	$this->_backendUrl = $backendUrl;
	$this->HelperBackend = $HelperBackend;
    }

	public function beforeSetLayout(\Magento\Sales\Block\Adminhtml\Order\View $view)
	{
		$message ='Are you sure you want to delete this?';
		$adminurl=$this->HelperBackend->getHomePageUrl();
		$params = array('order_id'=>$view->getOrderId());
        $url = $this->_backendUrl->getUrl("orderdelete/sales/delete", $params);
		$view->addButton(
			'order_delete',
			[
				'label' => __('Delete'),
				'class' => 'myclass',
				'onclick' => "confirmSetLocation('{$message}', '{$url}')"
			]
		);


	}
	
    public function getCustomUrl()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $urlManager = $objectManager->get('Magento\Framework\Url');
        return $urlManager->getUrl('orderdelete/sales/delete');
        //$this->urlBuilder->getUrl('namespace_modulename/adminhtml/moduledirectoryname/index');
    }
}
