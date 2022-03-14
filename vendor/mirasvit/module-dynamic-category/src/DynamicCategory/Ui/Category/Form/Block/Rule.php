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
 * @package   mirasvit/module-dynamic-category
 * @version   1.0.17
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);

namespace Mirasvit\DynamicCategory\Ui\Category\Form\Block;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget;
use Mirasvit\Core\Service\SerializeService;
use Mirasvit\DynamicCategory\Registry;

class Rule extends Widget
{
    protected $_nameInLayout = 'rule';

    protected $_template     = 'Mirasvit_DynamicCategory::category/rule.phtml';

    private   $conditions;

    private   $registry;

    public function __construct(
        Conditions $conditions,
        Registry $registry,
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->conditions = $conditions;
        $this->registry   = $registry;
    }

    public function getConditionsHtml(): string
    {
        return $this->conditions->toHtml();
    }

    public function getRequestUrl(): string
    {
        $category = $this->registry->getCurrentCategory();

        return $this->getUrl('mst_dynamic_category/category/preview', [
            'categoryId' => $category->getId(),
            'ajax'       => true,
        ]);
    }

    public function getLoaderUrl(): string
    {
        return $this->getUrl('mst_dynamic_category/category/load', [
            'ajax' => true,
        ]);
    }

    public function getPreviewJson(): string
    {
        return SerializeService::encode([
            'previewUrl' => $this->getRequestUrl(),
            'loaderUrl'  => $this->getLoaderUrl(),
        ]);
    }
}
