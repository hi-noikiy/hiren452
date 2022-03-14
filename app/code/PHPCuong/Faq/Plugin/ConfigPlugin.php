<?php 

namespace PHPCuong\Faq\Plugin;

Class ConfigPlugin {
	
	public function afterAssignProductToOption(\Magento\ConfigurableProduct\Model\Product\Type\Configurable $subject,$optionProduct, $option, $product)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/ConfigPlugin.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		//$logger->info('test');
		//print_r($optionProduct->getData()); //not working		
		//$logger->info($optionProduct->getData('entity_id'));		
		//$logger->info($option->getItem()->getId());
		//$logger->info($product->getId());		
		$logger->info( print_r($product->getData(), true ));
        //return $this;
    }
}