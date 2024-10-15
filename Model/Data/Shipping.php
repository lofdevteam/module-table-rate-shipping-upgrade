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

namespace Lof\TableRateShipping\Model\Data;

use Lof\TableRateShipping\Api\Data\TableRateShippingInterface;
use Lof\TableRateShipping\Api\Data\TableRateShippingInterfaceFactory;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\DataObjectHelper;

class Shipping extends \Magento\Framework\Api\AbstractExtensibleObject implements TableRateShippingInterface
{

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getLofshippingId()
    {
        return $this->_get(self::LOFMPSHIPPING_ID);
    }

    /**
     * @param int $id
     * @return TableRateShippingInterface|Shipping
     */
    public function setLofshippingId($id)
    {
        return $this->setData(self::LOFMPSHIPPING_ID, $id);
    }

    /**
     * @param int $partnerId
     * @return \Lof\TableRateShipping\Api\Data\TableRateShippingMethodInterface|Shipping
     */
    public function setPartnerId($partnerId)
    {
        return $this->setData(self::PARTNER_ID, $partnerId);
    }

    /**
     * @return array|mixed|string|null
     */
    public function getPartnerId()
    {
        return $this->_get(self::PARTNER_ID);
    }

    /**
     * @param int $websiteId
     * @return \Lof\TableRateShipping\Api\Data\TableRateShippingMethodInterface|Shipping
     */
    public function setWebsiteId($websiteId)
    {
        return $this->setData(self::WEBSITE_ID, $websiteId);
    }

    /**
     * @return array|Int|mixed|null
     */
    public function getWebsiteId()
    {
        return $this->_get(self::WEBSITE_ID);
    }

    /**
     * @param string $countryId
     * @return \Lof\TableRateShipping\Api\Data\TableRateShippingMethodInterface|Shipping
     */
    public function setDestCountryId($countryId)
    {
        return $this->setData(self::DEST_COUNTRY_ID, $countryId);
    }

    /**
     * @return array|string|mixed|null
     */
    public function getDestCountryId()
    {
        return $this->_get(self::DEST_COUNTRY_ID);
    }

    /**
     * @param int $regionId
     * @return \Lof\TableRateShipping\Api\Data\TableRateShippingMethodInterface|Shipping
     */
    public function setDestRegionId($regionId)
    {
        return $this->setData(self::DEST_REGION_ID, $regionId);
    }

    /**
     * @return array|int|mixed|null
     */
    public function getDestRegionId()
    {
        return $this->_get(self::DEST_REGION_ID);
    }

    /**
     * @param string $desZip
     * @return \Lof\TableRateShipping\Api\Data\TableRateShippingMethodInterface|Shipping
     */
    public function setDestZip($desZip)
    {
        return $this->setData(self::DEST_ZIP, $desZip);
    }

    /**
     * @return array|mixed|string|null
     */
    public function getDestZip()
    {
        return $this->_get(self::DEST_ZIP);
    }

    /**
     * @param string $zipTo
     * @return \Lof\TableRateShipping\Api\Data\TableRateShippingMethodInterface|Shipping
     */
    public function setDestZipTo($zipTo)
    {
        return $this->setData(self::DEST_ZIP_TO, $zipTo);
    }

    /**
     * @return array|mixed|string|null
     */
    public function getDestZipTo()
    {
        return $this->_get(self::DEST_ZIP_TO);
    }

    /**
     * @param float $price
     * @return \Lof\TableRateShipping\Api\Data\TableRateShippingMethodInterface|Shipping
     */
    public function setPrice($price)
    {
        return $this->setData(self::PRICE, $price);
    }

    /**
     * @return array|float|mixed|null
     */
    public function getPrice()
    {
        return $this->_get(self::PRICE);
    }

    /**
     * @param float $weight
     * @return \Lof\TableRateShipping\Api\Data\TableRateShippingMethodInterface|Shipping
     */
    public function setWeightFrom($weight)
    {
        return $this->setData(self::WEIGHT_FROM, $weight);
    }

    /**
     * @return array|float|mixed|null
     */
    public function getWeightFrom()
    {
        return $this->_get(self::WEIGHT_FROM);
    }

    /**
     * @param float $weight
     * @return \Lof\TableRateShipping\Api\Data\TableRateShippingMethodInterface|Shipping
     */
    public function setWeightTo($weight)
    {
        return $this->setData(self::WEIGHT_TO, $weight);
    }

    /**
     * @return array|float|mixed|null
     */
    public function getWeightTo()
    {
        return $this->_get(self::WEIGHT_TO);
    }

    /**
     * @param int $shippingMethodId
     * @return \Lof\TableRateShipping\Api\Data\TableRateShippingMethodInterface|Shipping
     */
    public function setShippingMethodId($shippingMethodId)
    {
        return $this->setData(self::SHIPPING_METHOD_ID, $shippingMethodId);
    }

    /**
     * @return array|int|mixed|null
     */
    public function getShippingMethodId()
    {
        return $this->_get(self::SHIPPING_METHOD_ID);
    }

    /**
     * @param int $freeShipping
     * @return \Lof\TableRateShipping\Api\Data\TableRateShippingMethodInterface|Shipping
     */
    public function setFreeShipping($freeShipping)
    {
        return $this->setData(self::FREE_SHIPPING, $freeShipping);
    }

    /**
     * @return array|int|mixed|null
     */
    public function getFreeShipping()
    {
        return $this->_get(self::FREE_SHIPPING);
    }

    /**
     * @return array|mixed|string|null
     */
    public function getMethodName()
    {
        return $this->_get(self::METHOD_NAME);
    }

    /**
     * @param string $name
     * @return \Lof\TableRateShipping\Api\Data\TableRateShippingMethodInterface|Shipping
     */
    public function setMethodName($name)
    {
        return $this->setData(self::METHOD_NAME, $name);
    }

    /**
     * @return \Lof\TableRateShipping\Api\Data\TableRateShippingExtensionInterface|\Magento\Framework\Api\ExtensionAttributesInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @param \Lof\TableRateShipping\Api\Data\TableRateShippingExtensionInterface $extensionAttributes
     * @return Shipping|void
     */
    public function setExtensionAttributes(
        \Lof\TableRateShipping\Api\Data\TableRateShippingExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
