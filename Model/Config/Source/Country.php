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

namespace Lof\TableRateShipping\Model\Config\Source;

class Country implements \Magento\Framework\Option\ArrayInterface
{
    protected $country;

    public function __construct(\Lof\TableRateShipping\Block\Shipping\Shipping $country){
        $this->country = $country;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $country = $this->country->getCountryOptionArray();
        $data = array();

        foreach ($country as $key => $_country) {
            $data[] = [
                'value' => $_country['value'],
                'label' => $_country['label']
            ];
        }

        return $data;
    }

}
