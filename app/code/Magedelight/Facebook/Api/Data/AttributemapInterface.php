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
namespace Magedelight\Facebook\Api\Data;

/**
 * Attribute Mapping interface.
 *
 * @api
 */
interface AttributemapInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ENTITY_ID = 'mapping_id';
    const MAGE_ATTRIBUTE = 'mage_attribute';
    const FB_ATTRIBUTE = 'fb_attribute';
    
    /**
     * Get ID.
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set ID.
     *
     * @param int $id
     *
     * @return \Magedelight\Facebook\Api\Data\AttributemapInterface
     */
    public function setId($id);
    
    /**
     * Get MageAttribute.
     *
     * @return string|null
     */
    public function getMageAttribute();

    /**
     * Set MageAttribute.
     *
     * @param int $mageAttribute
     *
     * @return \Magedelight\Facebook\Api\Data\AttributemapInterface
     */
    public function setMageAttribute($mageAttribute);
    
    /**
     * Get FbAttribute.
     *
     * @return string|null
     */
    public function getFbAttribute();

    /**
     * Set FbAttribute.
     *
     * @param string|null $fbattribute
     *
     * @return \Magedelight\Facebook\Api\Data\AttributemapInterface
     */
    public function setFbAttribute($fbattribute);
}