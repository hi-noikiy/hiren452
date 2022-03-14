<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://www.magezon.com/license
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_ProductPagePdf
 * @copyright Copyright (C) 2020 Magezon (https://www.magezon.com)
 */

namespace Magezon\ProductPagePdf\Model;

use Magento\Rule\Model\AbstractModel;
use Magezon\ProductPagePdf\Api\Data\ProfileInterface;

class Profile extends AbstractModel implements ProfileInterface
{
    /**#@+
     * Profile's Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;
    /**#@-*/

    /**
     * CMS page cache tag
     */
    const CACHE_TAG = 'productpagepdf_profile';

    /**#@-*/
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'productpagepdf_profile';
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\CatalogRule\Model\Rule\Condition\CombineFactory
     */
    protected $_combineFactory;

    /**
     * @var \Magento\CatalogRule\Model\Rule\Action\CollectionFactory
     */
    protected $_actionCollectionFactory;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $mediaDirectory;

    /**
     * File constructor.
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\CatalogRule\Model\Rule\Condition\CombineFactory $combineFactory
     * @param \Magento\CatalogRule\Model\Rule\Action\CollectionFactory $actionCollectionFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $relatedCacheTypes
     * @param array $data
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\CatalogRule\Model\Rule\Condition\CombineFactory $combineFactory,
        \Magento\CatalogRule\Model\Rule\Action\CollectionFactory $actionCollectionFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $relatedCacheTypes = [],
        array $data = []
    ) {
        $this->_storeManager = $storeManager;
        $this->_combineFactory = $combineFactory;
        $this->_actionCollectionFactory = $actionCollectionFactory;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        parent::__construct(
            $context,
            $registry,
            $formFactory,
            $localeDate,
            $resource,
            $resourceCollection,
            $data
        );
    }

    protected function _construct()
    {
        $this->_init(\Magezon\ProductPagePdf\Model\ResourceModel\Profile::class);
    }

    /**
     * Getter for rule conditions collection
     *
     * @return \Magento\Rule\Model\Condition\Combine
     */
    public function getConditionsInstance()
    {
        return $this->_combineFactory->create();
    }

    /**
     * Getter for rule actions collection
     *
     * @return \Magento\CatalogRule\Model\Rule\Action\Collection
     */
    public function getActionsInstance()
    {
        return $this->_actionCollectionFactory->create();
    }

    /**
     * Get conditions field set id.
     *
     * @param string $formName
     * @return string
     */
    public function getConditionsFieldSetId($formName = '')
    {
        return $formName . 'rule_conditions_fieldset_' . $this->getId();
    }

    /**
     * Get actions field set id.
     *
     * @param string $formName
     * @return string
     */
    public function getActionsFieldSetId($formName = '')
    {
        return $formName . 'rule_actions_fieldset_' . $this->getId();
    }

    /**
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId(), self::CACHE_TAG];
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->getData(self::PROFILE_ID);
    }

    /**
     * @return string
     */
    public function getName() 
    {
        return parent::getData(self::NAME);
    }

    /**
     * @return string|null
     */
    public function getProfile()   
    {
        return parent::getData(self::PROFILE);
    }

    /**
     * Is active
     *
     * @return bool|null
     */
    public function isActive() 
    {
        return parent::getData(self::IS_ACTIVE);
    }

    /**
     * Get from date
     *
     * @return string|null
     */
    public function getFromDate() 
    {
        return parent::getData(self::FROM_DATE);
    }

    /**
     * Get to date
     *
     * @return string|null
     */
    public function getToDate()
    {
        return parent::getData(self::TO_DATE);
    }

    /**
     * @return string|null
     */
    public function getCreationTime()
    {
        return parent::getData(self::CREATION_TIME);
    }

    /**
     * @return string|null
     */
    public function getUpdateTime()
    {
        return parent::getData(self::UPDATE_TIME);
    }

    /**
     * @return bool|null
     */
    public function getAutoDownload()
    {
        return parent::getData(self::AUTO_DOWNLOAD);
    }

    /**
     * @return int|null
     */
    public function getButtonType() 
    {
        return parent::getData(self::BUTTON_TYPE);
    }

    /**
     * @return int|null
     */
    public function getButtonPosition()
    {
        $buttonPosition = parent::getData(self::BUTTON_POSITION);
        if ($buttonPosition == 0) $buttonPosition = 2;
        return $buttonPosition;
    }

    /**
     * @return int|null
     */
    public function getPriority()
    {
        return parent::getData(self::PRIORITY);
    }

    /**
     * Get conditions serialized
     *
     * @return string|null
     */
    public function getConditionsSerialized()
    {
        return parent::getData(self::CONDITIONS_SERIALIZED);
    }

    /**
     * @return string|null
     */
    public function getProductTypes()
    {
        return parent::getData(self::PRODUCT_TYPES);
    }

    /**
     * @param int $id
     * @return ProfileInterface
     */
    public function setId($id)
    {
        return $this->setData(self::PROFILE_ID, $id);  
    }

    /**
     * @param string $name
     * @return ProfileInterface
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * @param string $profile
     * @return ProfileInterface
     */
    public function setProfile($profile)
    {
        return $this->setData(self::PROFILE, $profile);
    }

    /**
     * @param int|bool $isActive
     * @return ProfileInterface
     */
    public function setIsActive($isActive)
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }

    /**
     * @param string $fromDate
     * @return ProfileInterface
     */
    public function setFromDate($fromDate)
    {
        return $this->setData(self::FROM_DATE, $fromDate);
    }

    /**
     * @param string $toDate
     * @return ProfileInterface
     */
    public function setToDate($toDate)
    {
        return $this->setData(self::TO_DATE, $toDate);
    }

    /**
     * @param string $creationTime
     * @return ProfileInterface
     */
    public function setCreationTime($creationTime)
    {
        return $this->setData(self::CREATION_TIME, $creationTime);
    }

    /**
     * @param string $updateTime
     * @return ProfileInterface
     */
    public function setUpdateTime($updateTime)
    {
        return $this->setData(self::UPDATE_TIME, $updateTime);
    }

    /**
     * @param int|bool $autoDownload
     * @return ProfileInterface
     */
    public function setAutoDownload($autoDownload)
    {
        return $this->setData(self::AUTO_DOWNLOAD, $autoDownload);
    }

    /**
     * @param int $buttonType
     * @return ProfileInterface
     */
    public function setButtonType($buttonType)
    {
        return $this->setData(self::BUTTON_TYPE, $buttonType);
    }

    /**
     * @param int $buttonPosition
     * @return ProfileInterface
     */
    public function setButtonPosition($buttonPosition)
    {
        return $this->setData(self::BUTTON_POSITION, $buttonPosition);
    }

    /**
     * @param int $priority
     * @return ProfileInterface
     */
    public function setPriority($priority)
    {
        return $this->setData(self::PRIORITY, $priority);
    }

    /**
     * Set conditions serialized
     *
     * @param string $conditionsSerialized
     * @return ProfileInterface
     */
    public function setConditionsSerialized($conditionsSerialized)
    {
        return $this->setData(self::CONDITIONS_SERIALIZED, $conditionsSerialized);
    }

    /**
     * @param string $productTypes
     * @return ProfileInterface
     */
    public function setProductTypes($productTypes)
    {
        return $this->setData(self::PRODUCT_TYPES, $productTypes);
    }
}
