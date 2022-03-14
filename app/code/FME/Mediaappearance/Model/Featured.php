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
namespace FME\Mediaappearance\Model;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class IsActive
 */
class Featured implements OptionSourceInterface
{
    
    /**
     * @var \Magento\Cms\Model\
     */
    protected $mediaappearance;

    /**
     * Constructor
     *
     * @param \Magento\Cms\Model\ $mediaappearance
     */
    public function __construct(\FME\Mediaappearance\Model\Mediaappearance  $mediaappearance)
    {
        $this->mediaappearance = $mediaappearance;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options[] = ['label' => '', 'value' => ''];
        $availableOptions = $this->mediaappearance->getFeaturedStatuses();
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }
}
