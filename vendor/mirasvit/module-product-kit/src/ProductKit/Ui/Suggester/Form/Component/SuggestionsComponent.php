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



namespace Mirasvit\ProductKit\Ui\Suggester\Form\Component;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Asset\Repository as AssetRepository;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\AbstractComponent;

class SuggestionsComponent extends AbstractComponent
{
    private $assetRepository;

    private $url;

    public function __construct(
        AssetRepository $assetRepository,
        UrlInterface $url,
        ContextInterface $context,
        array $components = [],
        array $data = []
    ) {
        $this->assetRepository = $assetRepository;

        $this->url = $url;

        parent::__construct($context, $components, $data);
    }

    public function getComponentName()
    {
        return 'suggestions';
    }

    public function prepare()
    {
        $config = $this->getData('config');

        $config = array_merge($config, [
            'component'  => 'Mirasvit_ProductKit/js/suggester/form/component/suggestions',
            'url'        => $this->url->getUrl('product_kit/suggester/suggest'),
            'createUrl'  => $this->url->getUrl('product_kit/suggester/createKit'),
            'successUrl' => $this->url->getUrl('product_kit/suggester/index'),
            'loaderImg'  => $this->getViewFileUrl('images/loader-2.gif'),

        ]);

        $this->setData('config', $config);

        parent::prepare();
    }

    /**
     * @param string $img
     *
     * @return string
     */
    private function getViewFileUrl($img)
    {
        $params = [];

        return $this->assetRepository->getUrlWithParams($img, $params);
    }
}
