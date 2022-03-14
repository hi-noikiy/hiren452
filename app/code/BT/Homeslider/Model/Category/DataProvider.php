<?php
namespace BT\Homeslider\Model\Category;
 
class DataProvider extends \Magento\Catalog\Model\Category\DataProvider
{
 
	protected function getFieldsMap()
	{
    	$fields = parent::getFieldsMap();
        $fields['content'][] = 'home_category_image'; // custom image field
    	
    	return $fields;
	}
}