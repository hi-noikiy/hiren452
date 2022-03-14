<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Controller\Adminhtml;

class Affiliate extends \Plumrocket\Base\Controller\Adminhtml\Actions
{
    const ADMIN_RESOURCE = 'Plumrocket_Affiliate::praffiliate';

    /**
     * Form session key
     * @var string
     */
    protected $_formSessionKey  = 'affiliate_form_data';

    /**
     * Model of main class
     * @var string
     */
    protected $_modelClass      = 'Plumrocket\Affiliate\Model\Affiliate';

    /**
     * Active menu
     * @var string
     */
    protected $_activeMenu     = 'Plumrocket_Affiliate::praffiliate';

    /**
     * Object Title
     * @var string
     */
    protected $_objectTitle     = 'Affiliate Program';

    /**
     * Object titles
     * @var string
     */
    protected $_objectTitles    = 'Affiliate Programs';

    /**
     * Status field
     * @var string
     */
    protected $_statusField     = 'status';

    /**
     * Affiliate Manager
     * @var \Plumrocket\Affiliate\Model\AffiliateManager
     */
    protected $affiliateManager;

    /**
     * Type Factory
     * @var \Plumrocket\Affiliate\Model\TypeFactory
     */
    protected $typeFactory;

    /**
     * Affiliate constructor.
     *
     * @param \Magento\Backend\App\Action\Context          $context
     * @param \Plumrocket\Affiliate\Model\AffiliateManager $affiliateManager
     * @param \Plumrocket\Affiliate\Model\TypeFactory      $typeFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Plumrocket\Affiliate\Model\AffiliateManager $affiliateManager,
        \Plumrocket\Affiliate\Model\TypeFactory $typeFactory
    ) {
        parent::__construct($context);
        $this->affiliateManager = $affiliateManager;
        $this->typeFactory      = $typeFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {

        if ($this->getRequest()->getActionName() == 'delete' || $this->getRequest()->getActionName() == 'massStatus') {
            parent::execute();
            return;
        }

        $typeId = null;
        if ($this->getRequest()->getParam('type_id')) {
            $typeId = $this->getRequest()->getParam('type_id');
        } elseif ($affiliateId = $this->getRequest()->getParam('id')) {
            $affiliate = $this->affiliateManager->createAffiliateByParam($this->_modelClass)->load($affiliateId);
            if ($affiliate->getTypeId()) {
                $typeId = $affiliate->getTypeId();
            }
        } else {
            if ($this->getRequest()->getActionName() == 'edit') {
                $this->_redirect('praffiliate/affiliate/new');
            }
        }

        if ($typeId !== null) {
            $type = $this->typeFactory->create()->load($typeId);

            if (class_exists($this->_modelClass . '\\' . ucfirst($type->getKey()))) {
                $this->_modelClass = $this->_modelClass . '\\' . ucfirst($type->getKey());
            }
        }

        parent::execute();
    }

    /**
     * {@inheritdoc}
     */
    protected function _setPageTitle()
    {
        $model = $this->_getModel();
        $this->_view->getPage()->getConfig()->getTitle()->prepend('Edit Affiliate Program ' . $model->getTitle());

        return $this;
    }
}
