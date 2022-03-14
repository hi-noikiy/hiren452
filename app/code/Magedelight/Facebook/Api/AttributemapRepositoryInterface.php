<?php
/**
 * Magedelight
 * Copyright (C) 2019 Magedelight <info@magedelight.com>
 *
 * @category Magedelight
 * @package Magedelight_Facebook
 * @copyright Copyright (c) 2019 Mage Delight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 */
namespace Magedelight\Facebook\Api;

/**
 * Facebook token repository interface.
 *
 * @api
 */
interface AttributemapRepositoryInterface
{
    /**
     * Lists facebook attribute mapping that match specified search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria The search criteria.
     * @return \Magedelight\Facebook\Api\Data\AttributemapSearchResultsInterface Facebook attribute search result interface.
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Loads a specified facebook map.
     *
     * @param int $entityId The attribute mapping entity ID.
     * @return \Magedelight\Facebook\Api\Data\AttributemapInterface Attribute Mapping interface.
     */
    public function getById($entityId);

    /**
     * Delete Attribute Map
     *
     * @param \Magedelight\Facebook\Api\Data\AttributemapInterface $attributemap
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(\Magedelight\Facebook\Api\Data\AttributemapInterface $attributemap);
   
    /**
     * Deletes a specified attribute mapping.
     *
     * @param int $entityId The attribute mapping entity ID.
     * @return bool
     */
    public function deleteById($entityId);

    /**
     * Performs persist operations for a specified attribute mapping.
     *
     * @param \Magedelight\Facebook\Api\Data\AttributemapInterface $attributemap.
     * @return \Magedelight\Facebook\Api\Data\AttributemapInterface Facebook attribute map interface.
     * @since 100.1.0
     */
    public function save(Data\AttributemapInterface $attributemap);
}
