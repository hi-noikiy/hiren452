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
 * @package     Plumrocket_Bestsellers
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */
declare(strict_types=1);

namespace Plumrocket\Bestsellers\Model\Report;

class Interval
{
    /**
     * @var array
     */
    private $intervals = [];

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    private $localeDate;

    /**
     * @var object[]
     */
    private $customIntervals;

    /**
     * Interval constructor.
     *
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param array                                                $customIntervals
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        array $customIntervals = []
    ) {
        $this->localeDate = $localeDate;
        $this->customIntervals = $customIntervals;
    }

    /**
     * @param string $period
     * @return \DateTime[]|false
     */
    public function getByPeriod(string $period)
    {
        if (! isset($this->intervals[$period])) {
            $interval = $this->tryUseCustomIntervals($period);

            if (! $interval) {
                switch ($period) {
                    case \Plumrocket\Bestsellers\Api\ProductIdsProviderInterface::PERIOD_DAY:
                        $interval = [
                            'start' => $this->localeDate->scopeDate(null, 'today'),
                            'end' => $this->localeDate->scopeDate(null, 'today'),
                        ];
                        break;
                    case \Plumrocket\Bestsellers\Api\ProductIdsProviderInterface::PERIOD_WEEK:
                        $interval = [
                            'start' => $this->localeDate->scopeDate(null, '-7 days'),
                            'end' => $this->localeDate->scopeDate(null, 'now'),
                        ];
                        break;
                    case \Plumrocket\Bestsellers\Api\ProductIdsProviderInterface::PERIOD_MONTH:
                        $interval = [
                            'start' => $this->localeDate->scopeDate(null, 'now')->modify('-1 Month'),
                            'end' => $this->localeDate->scopeDate(null, 'now'),
                        ];
                        break;
                    case \Plumrocket\Bestsellers\Api\ProductIdsProviderInterface::PERIOD_YEAR:
                        $interval = [
                            'start' => $this->localeDate->scopeDate(null, 'now')->modify('-12 Month'),
                            'end' => $this->localeDate->scopeDate(null, 'now'),
                        ];
                        break;
                    default:
                        $interval = false;
                }
            }

            $this->intervals[$period] = $interval;
        }

        return $this->intervals[$period];
    }

    /**
     * @param string $period
     * @return bool|mixed
     */
    private function tryUseCustomIntervals(string $period)
    {
        if (! empty($this->customIntervals) && isset($this->customIntervals[$period])) {
            try {
                $interval = $this->customIntervals[$period]->getInterval();
            } catch (\Exception $exception) {
                $interval = false;
            }
        } else {
            $interval = false;
        }

        return $interval;
    }
}
