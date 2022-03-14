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
namespace FME\Mediaappearance\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();
        /**
         * Create table 'Media Gallery Table'
         */
        //vreate Media gallery table

        $table = $installer->getConnection()
        ->newTable($installer->getTable('fme_mediagallery'))
        ->addColumn(
            'mediagallery_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'fme_mediagallery Id'
        )
        ->addColumn(
            'gal_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Gallery Name'
        )
        ->addColumn(
            'gorder',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'Gallery Order'
        )
        ->addColumn(
            'description',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => true, 'default' => null],
            'Description'
        )
        ->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => 0],
            'Status'
        )
        ->addColumn(
            'created_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Created Time'
        )
        ->addColumn(
            'update_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Updated Time'
        )
        ->addIndex(
            $installer->getIdxName('fme_mediagallery', ['mediagallery_id']),
            ['mediagallery_id']
        )
        ->setComment('fme_mediagallery Table');




    $installer->getConnection()->createTable($table);
        //End of Media gallery 
        $table = $installer->getConnection()
            ->newTable($installer->getTable('fme_mediaappearance'))
            ->addColumn(
                'mediaappearance_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )

            ->addColumn(
                'mediatype',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false],
                'Type'
            )
            ->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'default' => 0],
                'Status'
            )
            ->addColumn(
                'media_title',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => true, 'default' => null],
                'Description'
            )
            ->addColumn(
                'filename',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => true, 'default' => null],
                'File Name'
            )
            ->addColumn(
                'filethumb',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => true, 'default' => null],
                'File Thumb'
            )
            ->addColumn(
                'videourl',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => true, 'default' => null],
                'Video Url'
            )
            ->addColumn( 
                'featured_media',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => true, 'default' => null],
                'Featured'
            )
            ->addColumn(
                'media_desp',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => true, 'default' => null],
                'Description'
            )
            ->addColumn(
                'mediagallery_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Media gallery Id'
            )
            
            ->addForeignKey(
                $installer->getFkName('mediagallery_img_ph', 'mediagallery_id', 'fme_mediagallery', 'mediagallery_id'),
                'mediagallery_id',
                $installer->getTable('fme_mediagallery'),
                'mediagallery_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment('Media Appearance  Table');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'Media Gallery Store'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('fme_media_store'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )
            ->addColumn(
                'mediagallery_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Media Gallery Id'
            )
            ->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Store Id'
            )
            ->addForeignKey(
                $installer->getFkName('media_store_ibfk_1', 'mediagallery_id', 'fme_mediagallery', 'mediagallery_id'),
                'mediagallery_id',
                $installer->getTable('fme_mediagallery'),
                'mediagallery_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment('Media Store Table');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'Media Gallery Products'
         */
        


        $table = $installer->getConnection()
        ->newTable($installer->getTable('fme_tags'))
        ->addColumn(
            'tag_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'fme_mediagallery Id'
        )
        ->addColumn(
            'tag_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Gallery Name'
        )
        
        ->addIndex(
            $installer->getIdxName('fme_tags', ['tag_id']),
            ['tag_id']
        )
        ->setComment('fme_mediagallery Table');




    $installer->getConnection()->createTable($table);



    $table = $installer->getConnection()
            ->newTable($installer->getTable('fme_media_tags'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )
            ->addColumn(
                'mediagallery_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Media Gallery Id'
            )
            ->addColumn(
                'tag_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Tag Id'
            )
            ->addForeignKey(
                $installer->getFkName('media_tag_ibfk_1', 'mediagallery_id', 'fme_mediagallery', 'mediagallery_id'),
                'mediagallery_id',
                $installer->getTable('fme_mediagallery'),
                'mediagallery_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment('Media Tag Table');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'Media Blocks Table'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('fme_mediagallery_products'))
            ->addColumn(
                'mediagallery_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Photogallery Id'
            )
            ->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'unsigned' => true, 'default' => 0],
                'Product Id'
            )
            ->addForeignKey(
                $installer->getFkName('fme_mediagallery_products', 'mediagallery_id', 'fme_mediagallery', 'mediagallery_id'),
                'mediagallery_id',
                $installer->getTable('fme_mediagallery'),
                'mediagallery_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment('Media Products Table');
        $installer->getConnection()->createTable($table);








        $table = $installer->getConnection()
        ->newTable($installer->getTable('fme_mediagallery_category'))
        ->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Id'
        )
        ->addColumn(
            'mediagallery_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'fme_mediagallery Id'
        )
        ->addColumn(
            'category_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Category Id'
        )
        ->addForeignKey(
            $installer->getFkName('mediagallery_category_ibssfk_1', 'mediagallery_id', 'fme_mediagallery', 'mediagallery_id'),
            'mediagallery_id',
            $installer->getTable('fme_mediagallery'),
            'mediagallery_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )
        ->setComment('Product attachemnts cms Table');
        $installer->getConnection()->createTable($table);





        $table = $installer->getConnection()
        ->newTable($installer->getTable('fme_mediagallery_cmspage'))
        ->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Id'
        )
        ->addColumn(
            'mediagallery_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'fme_mediagallery Id'
        )
        ->addColumn(
            'cms_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'CMS Id'
        )
        ->addForeignKey(
            $installer->getFkName('mediagallery_cmspage_ibfk_1', 'mediagallery_id', 'fme_mediagallery', 'mediagallery_id'),
            'mediagallery_id',
            $installer->getTable('fme_mediagallery'),
            'mediagallery_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )
        ->setComment('Product attachemnts cms Table');
        $installer->getConnection()->createTable($table);





        /*$table = $installer->getConnection()
            ->newTable($installer->getTable('fme_mediagallery_cmspage'))
            ->addColumn(
                'mediagallery_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Photogallery Id'
            )
            ->addColumn(
                'cmspage_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'unsigned' => true, 'default' => 0],
                'Product Id'
            )
            ->addForeignKey(
                $installer->getFkName('fme_mediagallery_cmspage', 'mediagallery_id', 'fme_mediagallery', 'mediagallery_id'),
                'mediagallery_id',
                $installer->getTable('fme_mediagallery'),
                'mediagallery_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment('Media CMS Page Table');
        $installer->getConnection()->createTable($table);


*/






        $installer->endSetup();
    }
}
