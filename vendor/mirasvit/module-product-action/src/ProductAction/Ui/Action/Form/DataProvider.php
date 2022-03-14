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
 * @package   mirasvit/module-product-action
 * @version   1.0.9
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);

namespace Mirasvit\ProductAction\Ui\Action\Form;

use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends AbstractDataProvider
{
    private $actionModifier;

    private $request;

    public function __construct(
        Modifier\ActionModifier $actionModifier,
        RequestInterface $request,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        $this->actionModifier = $actionModifier;
        $this->request        = $request;

        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getMeta(): array
    {
        return $this->actionModifier->modifyMeta(parent::getMeta());
    }

    public function setLimit($offset, $size)
    {
    }

    public function addField($field, $alias = null)
    {
    }

    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {

    }

    public function getData(): array
    {
        return [];
    }
}
