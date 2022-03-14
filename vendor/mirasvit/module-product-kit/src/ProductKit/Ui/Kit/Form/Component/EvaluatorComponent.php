<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-product-kit
 * @version   1.0.29
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\ProductKit\Ui\Kit\Form\Component;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\AbstractComponent;

class EvaluatorComponent extends AbstractComponent
{
    private $url;

    public function __construct(
        UrlInterface $url,
        ContextInterface $context,
        array $components = [],
        array $data = []
    ) {
        $this->url = $url;
        parent::__construct($context, $components, $data);
    }

    public function getComponentName()
    {
        return 'evaluator';
    }

    public function prepare()
    {
        $config = $this->getData('config');

        $config = array_merge($config, [
            'component' => 'Mirasvit_ProductKit/js/kit/form/component/evaluator',
            'url'       => $this->url->getUrl('product_kit/kit/evaluator'),
        ]);

        $this->setData('config', $config);

        parent::prepare();
    }
}
