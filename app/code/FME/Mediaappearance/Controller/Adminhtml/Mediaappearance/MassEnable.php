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
namespace FME\Mediaappearance\Controller\Adminhtml\Mediaappearance;

use FME\Mediaappearance\Controller\Adminhtml\AbstractMassStatus;

/**
 * Class MassDelete
 */
class MassEnable extends AbstractMassStatus
{

    /**
     * Field id
     */
    const ID_FIELD = 'mediagallery_id';

    /**
     * ResourceModel collection
     *
     * @var string
     */
    protected $collection = 'FME\Mediaappearance\Model\ResourceModel\Mediaappearance\Collection';

    /**
     * Page model
     *
     * @var string
     */
    protected $model = 'FME\Mediaappearance\Model\Mediaappearance';

    /**
     * Item status
     *
     * @var bool
     */
    protected $status = 1;
}
