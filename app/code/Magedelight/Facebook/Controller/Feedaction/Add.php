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

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Catalog\Api\ProductRepositoryInterface;
use \Magento\Checkout\Model\CartFactory;
use Magento\GroupedProduct\Ui\DataProvider\Product\Form\Modifier\CustomOptions;
use Magento\Catalog\Model\Product\Type as ProductType;
use Magento\Catalog\Model\Product\OptionFactory;

class Add extends Action 
{
    
    /**
     * 
     * @param Context $context
     */
    public function __construct(Context $context,
                                ProductRepositoryInterface $productRepository,
                                CartFactory $cartFactory,
                                OptionFactory $optionFactory
        ) 
    {
        $this->cartFactory = $cartFactory;
        $this->productRepository = $productRepository;
        $this->optionFactory = $optionFactory;
        parent::__construct($context);
    }
    
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $params = $this->getRequest()->getParams();
        if(isset($params['type']) && $params['type']==\Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE){
            try{
                $cart = $this->cartFactory->create();
                $parentProduct = $this->productRepository->get($params['parent']);
                $customOptions = $this->optionFactory->create()->getProductOptionCollection($parentProduct);
                if (!empty($customOptions->getData())) {
                    $productUrl  = $parentProduct->getProductUrl();
                    return $resultRedirect->setPath($productUrl);
                }
                $childProduct = $this->productRepository->get($params['sku']);
                $prodparams = [];
                $prodparams['product'] = $parentProduct->getId();
                $prodparams['qty'] = 1;
                $options = [];
                $productAttributeOptions = $parentProduct->getTypeInstance(true)->getConfigurableAttributesAsArray($parentProduct);
                foreach($productAttributeOptions as $option){
                    $options[$option['attribute_id']] =  $childProduct->getData($option['attribute_code']);
                }
                $prodparams['super_attribute'] = $options;
                $cart->addProduct($parentProduct, $prodparams);
                $cart->save();
                $this->messageManager->addSuccess(__("You added %1 to your cart.",$parentProduct->getName()));
            } catch (\Exception $ex) {
                $this->messageManager->addError(__("Something went wrong."));
            }
            
        }
        else {
            try {
                $sku = $params['sku'];
                $product = $this->productRepository->get($sku);
                $productType = $product->getTypeId();
                if ($productType == ProductType::TYPE_BUNDLE || $productType == CustomOptions::PRODUCT_TYPE_GROUPED || $product->getData('has_options')) {
                    $productUrl  = $product->getProductUrl();
                    return $resultRedirect->setPath($productUrl);
                }
                $simpleparams = [
                                   'product' => $product->getId(),
                                   'qty' => 1
                                ];
               
                $cart = $this->cartFactory->create(); 
                $cart->addProduct($product,$simpleparams);
                $cart->save();
                $this->messageManager->addSuccess(__("You added %1 to your cart.",$product->getName()));

            } catch (\Exception $ex) {
               // $this->messageManager->addError($ex->getMessage());
                $this->messageManager->addError(__("Something went wrong."));
            }
        }
        return $resultRedirect->setPath('checkout');
    }
}