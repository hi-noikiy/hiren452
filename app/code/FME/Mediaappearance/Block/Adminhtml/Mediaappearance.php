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
namespace FME\Mediaappearance\Block\Adminhtml;

class Mediaappearance extends \Magento\Backend\Block\Widget\Grid\Container
{
    
    /**
     * _construct
     *
     */
    public function _construct()
    {
        $this->_controller = 'adminhtml_mediaappearance';
        $this->_blockGroup = 'FME_Mediaappearance';
        $this->_headerText = __('Mediaappearance Manager');
        $this->_addButtonLabel = __('Add Gallery');
        parent::_construct();
    }
}
