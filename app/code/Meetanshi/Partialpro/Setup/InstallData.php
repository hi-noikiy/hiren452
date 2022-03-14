<?php
namespace Meetanshi\Partialpro\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    private $eavSetupFactory;
    
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }
    
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'apply_partial_payment',
            [
                'group' => 'Partial Payment',
                'label' => 'Apply Partial Payment',
                'type'  => 'int',
                'input' => 'boolean',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'required' => false,
                'sort_order' => 10,
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                'used_in_product_listing' => true,
                'visible_on_front' => false
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'no_installment',
            [
                'group' => 'Partial Payment',
                'label' => 'Number Of Installments ',
                'type'  => 'int',
                'input' => 'text',
                'required' => false,
                'sort_order' => 20,
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                'used_in_product_listing' => true,
                'visible_on_front' => false
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'calculation_down_payment',
            [
                'group' => 'Partial Payment',
                'label' => 'Calculate Down Payment On',
                'type'  => 'int',
                'input' => 'select',
                'source' => 'Meetanshi\Partialpro\Model\Config\Product\CalculationDownPayment',
                'required' => false,
                'sort_order' => 30,
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                'used_in_product_listing' => true,
                'visible_on_front' => false
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'down_payment',
            [
                'group' => 'Partial Payment',
                'label' => 'Down Payment Amount',
                'type'  => 'int',
                'input' => 'text',
                'required' => false,
                'sort_order' => 40,
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                'used_in_product_listing' => true,
                'visible_on_front' => false
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'cal_instamment_fee_payment',
            [
                'group' => 'Partial Payment',
                'label' => 'Calculate Partial Payment Fee in',
                'type'  => 'int',
                'input' => 'select',
                'source' => 'Meetanshi\Partialpro\Model\Config\Product\CalculationDownPayment',
                'required' => false,
                'sort_order' => 50,
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                'used_in_product_listing' => true,
                'visible_on_front' => false
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'installment_fee',
            [
                'group' => 'Partial Payment',
                'label' => 'Fee Amount',
                'type'  => 'int',
                'input' => 'text',
                'required' => false,
                'sort_order' => 60,
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                'used_in_product_listing' => true,
                'visible_on_front' => false
            ]
        );


        $setup->endSetup();
    }
}
