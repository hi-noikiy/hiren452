<?php

namespace Hiddentechies\Reviewspro\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;

class InstallSchema implements InstallSchemaInterface {

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        $installer = $setup;

        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.0.0') < 0) {
            $tableReview = $installer->getTable('hidden_reviewspro');
            $installer->run('CREATE TABLE ' . $tableReview . ' (
  `id` int(11) NOT NULL,
  `review_id` int(11) NOT NULL,
  `review_img` varchar(255) NOT NULL,
  `review_positive` int(11) NOT NULL,
  `review_negative` int(11) NOT NULL,
  `review_testimonial` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT \'0000-00-00 00:00:00\' ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1');
            $installer->run('ALTER TABLE ' . $tableReview . '
  ADD PRIMARY KEY (`id`)');
            $installer->run('ALTER TABLE ' . $tableReview . ' ADD UNIQUE(`review_id`);');
            $installer->run('ALTER TABLE ' . $tableReview . '
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT');
        }

        $installer->endSetup();
    }

}
