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



namespace Mirasvit\ProductKit\Console\Command;

use Magento\Framework\App\State;
use Magento\Framework\ObjectManagerInterface;
use Mirasvit\ProductKit\Model\Indexer;
use Mirasvit\ProductKit\Repository\KitRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ReindexCommand extends Command
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var State
     */
    private $appState;

    public function __construct(
        ObjectManagerInterface $objectManager,
        State $appState
    ) {
        $this->objectManager = $objectManager;
        $this->appState      = $appState;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('mirasvit:product-kit:reindex')
            ->setDescription('Product Kit Reindex');

        $this->addArgument('id', InputArgument::OPTIONAL, 'Kit Id');

        parent::configure();
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->appState->setAreaCode('frontend');
        } catch (\Exception $e) {
        }

        /** @var KitRepository $repository */
        $repository = $this->objectManager->create(KitRepository::class);

        foreach ($repository->getCollection() as $kit) {
            if ($input->getArgument('id') && $input->getArgument('id') != $kit->getId()) {
                $output->writeln(sprintf(
                    'Skip [%s] "%s"',
                    $kit->getId(),
                    $kit->getName()
                ));

                continue;
            }

            $output->write(sprintf(
                'Reindex [%s] "%s"...',
                $kit->getId(),
                $kit->getName()
            ));

            /** @var Indexer $indexer */
            $indexer = $this->objectManager->create(Indexer::class);

            $ts  = microtime(true);
            $mem = memory_get_usage();

            $indexer->executeFull([$kit->getId()]);

            $output->writeln(sprintf(
                "<info>done</info> (%s / %s)",
                round(microtime(true) - $ts, 4) . 's',
                round((memory_get_usage() - $mem) / 1024 / 1024, 2) . 'Mb'
            ));
        }
    }
}