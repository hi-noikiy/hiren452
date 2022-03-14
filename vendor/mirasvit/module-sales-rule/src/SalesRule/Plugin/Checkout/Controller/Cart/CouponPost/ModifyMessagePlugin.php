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
 * @package   mirasvit/module-sales-rule
 * @version   1.0.16
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SalesRule\Plugin\Checkout\Controller\Cart\CouponPost;

use Magento\Checkout\Model\Cart;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\SalesRule\Model\CouponFactory;
use Mirasvit\SalesRule\Repository\RuleRepository;

class ModifyMessagePlugin
{
    /**
     * @var MessageManagerInterface
     */
    private $messageManager;

    /**
     * @var Cart
     */
    private $cart;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var CouponFactory
     */
    private $couponFactory;

    /**
     * @var RuleRepository
     */
    private $ruleRepository;

    /**
     * ModifyMessagePlugin constructor.
     * @param MessageManagerInterface $messageManager
     * @param Cart $cart
     * @param RequestInterface $request
     * @param CouponFactory $couponFactory
     * @param RuleRepository $ruleRepository
     */
    public function __construct(
        MessageManagerInterface $messageManager,
        Cart $cart,
        RequestInterface $request,
        CouponFactory $couponFactory,
        RuleRepository $ruleRepository
    ) {
        $this->messageManager = $messageManager;
        $this->cart           = $cart;
        $this->request        = $request;
        $this->couponFactory  = $couponFactory;
        $this->ruleRepository = $ruleRepository;
    }

    /**
     * @param mixed $subject
     * @param mixed $result
     * @return mixed
     */
    public function afterExecute($subject, $result)
    {
        $couponCode = $this->request->getParam('remove') == 1 ? '' : trim($this->request->getParam('coupon_code'));

        if (!$couponCode) {
            return $result;
        }

        $coupon = $this->couponFactory->create()
            ->load($couponCode, 'code');

        if (!$coupon->getId()) {
            return $result;
        }

        $rule = $this->ruleRepository->getByParentId($coupon->getRuleId());

        if (!$rule) {
            return $result;
        }

        $appliedCoupon  = $this->cart->getQuote()->getCouponCode();
        $appliedRuleIds = $this->cart->getQuote()->getAppliedRuleIds();

        if ($appliedCoupon == $couponCode && strpos($appliedRuleIds, $coupon->getRuleId()) !== false) {
            if ($rule->getCouponSuccessMessage()) {
                $this->messageManager->getMessages(true);

                $this->messageManager->addSuccessMessage(
                    __($rule->getCouponSuccessMessage(), $couponCode)
                );
            }
        } else {
            if ($rule->getCouponErrorMessage()) {
                $this->messageManager->getMessages(true);

                $this->messageManager->addErrorMessage(
                    __($rule->getCouponErrorMessage(), $couponCode)
                );
            }
        }

        return $result;
    }
}