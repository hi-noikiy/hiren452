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

namespace Mirasvit\DynamicCategory\Console\Command;

use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ResourceModel\Category as ResourceCategory;
use Mirasvit\DynamicCategory\Repository\DynamicCategoryRepository;
use Mirasvit\DynamicCategory\Service\ReindexService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ReindexCommand extends Command
{
    private $categoryFactory;

    private $dynamicCategoryRepository;

    private $resourceCategory;

    private $reindexService;

    public function __construct(
        CategoryFactory $categoryFactory,
        DynamicCategoryRepository $dynamicCategoryRepository,
        ResourceCategory $resourceCategory,
        ReindexService $reindexService
    ) {
        $this->categoryFactory           = $categoryFactory;
        $this->dynamicCategoryRepository = $dynamicCategoryRepository;
        $this->resourceCategory          = $resourceCategory;
        $this->reindexService            = $reindexService;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('mirasvit:dynamic-category:reindex')
            ->setDescription('Reindex Dynamic Categories');

        $this->addArgument('id', InputArgument::OPTIONAL, 'Category Id');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        foreach ($this->dynamicCategoryRepository->getCollection() as $dynamicCategory) {
            $category = $this->categoryFactory->create();

            $this->resourceCategory->load($category, $dynamicCategory->getCategoryId());

            if ($input->getArgument('id')
                && (int)$input->getArgument('id') != $category->getId()) {
                $output->writeln(sprintf(
                    'Skip [%s] "%s"',
                    $category->getId(),
                    $category->getName()
                ));

                continue;
            }

            $output->write(sprintf(
                'Reindex [%s] "%s"...',
                $category->getId(),
                $category->getName()
            ));

            $ts  = microtime(true);
            $mem = memory_get_usage();

            $this->reindexService->reindexCategory($dynamicCategory);

            $output->writeln(sprintf(
                "<info>done</info> (%s / %s)",
                round(microtime(true) - $ts, 4) . 's',
                round((memory_get_usage() - $mem) / 1024 / 1024, 2) . 'Mb'
            ));
        }
    }
}
