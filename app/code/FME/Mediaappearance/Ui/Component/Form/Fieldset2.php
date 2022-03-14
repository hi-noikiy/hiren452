<?php  
/**
 * FME Extensions
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the fmeextensions.com license that is
 * available through the world-wide-web at this URL:
 * https://www.fmeextensions.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  FME
 * @author     Atta <support@fmeextensions.com>
 * @package   FME_Mediaappearance
 * @copyright Copyright (c) 2019 FME (http://fmeextensions.com/)
 * @license   https://fmeextensions.com/LICENSE.txt
 */
namespace FME\Mediaappearance\Ui\Component\Form;


use Magento\Framework\View\Element\UiComponent\ContextInterface;  
use Magento\Framework\View\Element\UiComponentInterface;  
use Magento\Ui\Component\Form\FieldFactory;  
use Magento\Ui\Component\Form\Fieldset as BaseFieldset;

class Fieldset2 extends BaseFieldset  
{
    /**
     * @var FieldFactory
     */
    private $fieldFactory;

    public function __construct(
        ContextInterface $context,
        array $components = [],
        array $data = [],
        FieldFactory $fieldFactory)
    {
        parent::__construct($context, $components, $data);
        $this->fieldFactory = $fieldFactory;
    }

    /**
     * Get components
     *
     * @return UiComponentInterface[]
     */
    public function getChildComponents()
    {
        $fields = [
            [
                'label' => __('Field Label From Code'),
                'value' => __('Field Value From Code'),
                'formElement' => 'input',
            ],
            [
                'label' => __('Another Field Label From Code'),
                'value' => __('Another Field Value From Code'),
                'formElement' => 'input',
            ],
            [
                'label' => __('Yet Another Field Label From Code'),
                'value' => __('Yet Another Field Value From Code'),
                'formElement' => 'input',
            ]
        ];

        foreach ($fields as $k => $fieldConfig) {
            $fieldInstance = $this->fieldFactory->create();
            $name = 'my_dynamic_field_' . $k;

            $fieldInstance->setData(
                [
                    'config' => $fieldConfig,
                    'name' => $name
                ]
            );

            $fieldInstance->prepare();
            $this->addComponent($name, $fieldInstance);
        }

        return parent::getChildComponents();
    }
}
