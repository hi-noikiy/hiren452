<?php

namespace Meetanshi\Partialpro\Model\Config\Source;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Payment\Model\Config;
use Magento\Framework\Option\ArrayInterface;

class Methods implements ArrayInterface
{
    protected $scopeConfigInterface;

    protected $paymentModelConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfigInterface,
        Config $paymentModelConfig
    )
    {

        $this->scopeConfigInterface = $scopeConfigInterface;
        $this->paymentModelConfig = $paymentModelConfig;
    }

    public function toOptionArray()
    {
        $payments = $this->paymentModelConfig->getActiveMethods();
        $methods = [];
        foreach ($payments as $paymentCode => $paymentModel) {
            if ($paymentCode == 'free')
                continue;

            $paymentTitle = $this->scopeConfigInterface->getValue('payment/' . $paymentCode . '/title');
            $methods[$paymentCode] = [
                'label' => $paymentTitle,
                'value' => $paymentCode
            ];
        }
        return $methods;
    }
}
