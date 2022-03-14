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

namespace Mirasvit\DynamicCategory\Plugin;

use Magento\Catalog\Model\Indexer\Category\Product\Action\Full;
use Mirasvit\DynamicCategory\Api\Data\DynamicCategoryInterface;
use Mirasvit\DynamicCategory\Repository\DynamicCategoryRepository;
use Mirasvit\DynamicCategory\Service\ReindexService;

/**
 * @see Full::execute()
 */
class FullReindexDynamicCategoryPlugin
{
    private $dynamicCategoryRepository;

    private $reindexService;

    public function __construct(
        DynamicCategoryRepository $dynamicCategoryRepository,
        ReindexService $reindexService
    ) {
        $this->dynamicCategoryRepository = $dynamicCategoryRepository;

        $this->reindexService = $reindexService;
    }

    /**
     * @param Full $subject
     *
     * @return null
     */
    public function beforeExecute(Full $subject)
    {
        $collection = $this->dynamicCategoryRepository->getCollection()
            ->addFieldToFilter(DynamicCategoryInterface::IS_ACTIVE, 1);

        /** @var DynamicCategoryInterface $dynamicCategory */
        foreach ($collection as $dynamicCategory) {
            $this->reindexService->reindexCategory($dynamicCategory);
        }

        return null;
    }
}
