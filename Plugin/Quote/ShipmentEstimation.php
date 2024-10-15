<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/terms
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_TableRateShipping
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */
namespace Lof\TableRateShipping\Plugin\Quote;

use Lof\TableRateShipping\Model\Carrier;

class ShipmentEstimation
{
    const FREESHIPPING = "freeshipping";
    const TABLERATE = "tablerate";
    /**
     * @var \Lof\TableRateShipping\Helper\Data
     */
    protected $helperData;

    /**
     * @param \Lof\TableRateShipping\Helper\Data $helperData
     */
    public function __construct(
        \Lof\TableRateShipping\Helper\Data $helperData
    ) {
        $this->helperData = $helperData;
    }

    /**
     * @inheritdoc
     */
    public function afterEstimateByExtendedAddress(\Magento\Quote\Model\ShippingMethodManagement $subject, $result)
    {
        if ($this->helperData->getIsDisableFreeShipping()) {
            $tablerateShipping = false;
            $freeshipping = false;
            $tablerateshipping = false;
            if ($result) {
                foreach ($result as $shipping) {
                    switch($shipping->getCarrierCode()) {
                        case Carrier::CODE:
                            $tablerateShipping = $shipping;
                            break;
                        case self::FREESHIPPING:
                            $freeshipping = $shipping;
                            break;
                        case self::TABLERATE:
                            if ((float)$shipping->getAmount() <= 0) {
                                $tablerateshipping = $shipping;
                            }
                            break;
                        default:
                            break;
                    }
                }
                if ($tablerateShipping && ($freeshipping || $tablerateshipping)) {
                    $excludeShippingTypes = $this->getShippingTypes();
                    $newResult = [];
                    foreach ($result as $shipping) {
                        if (!in_array($shipping->getCarrierCode(), $excludeShippingTypes)) {
                            $newResult[] = $shipping;
                        }
                    }
                    if (count($newResult)) {
                        $result = $newResult;
                    }
                }
            }
        }
        return $result;
    }

    /**
     * get ignore shipping types
     *
     * @return array
     */
    protected function getShippingTypes()
    {
        return [
            self::FREESHIPPING,
            self::TABLERATE
        ];
    }
}
