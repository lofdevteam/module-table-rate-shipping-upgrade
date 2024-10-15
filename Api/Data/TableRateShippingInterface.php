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

namespace Lof\TableRateShipping\Api\Data;

interface TableRateShippingInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const LOFMPSHIPPING_ID = 'lofshipping_id';
    const WEBSITE_ID = 'website_id';
    const DEST_COUNTRY_ID = 'dest_country_id';
    const DEST_REGION_ID = 'dest_region_id';
    const DEST_ZIP = 'dest_zip';
    const DEST_ZIP_TO = 'dest_zip_to';
    const PRICE = 'price';
    const WEIGHT_FROM = 'weight_from';
    const WEIGHT_TO = 'weight_to';
    const PARTNER_ID = 'partner_id';
    const SHIPPING_METHOD_ID = 'shipping_method_id';
    const FREE_SHIPPING = 'free_shipping';
    const METHOD_NAME = 'method_name';

    /**
     * Get TableRateShipping ID
     *
     * @return int|null
     */
    public function getLofshippingId();
    /**
     * Set TableRateShipping ID
     *
     * @param int $id
     * @return \Lof\TableRateShipping\Api\Data\TableRateShippingInterface
     */
    public function setLofshippingId($id);

    /**
     * Set PartnerId
     *
     * @param int $partnerId
     * @return \Lof\TableRateShipping\Api\Data\TableRateShippingMethodInterface
     */
    public function setPartnerId($partnerId);
    /**
     * Get PartnerId
     *
     * @return string|null
     */
    public function getPartnerId();

    /**
     * Set PartnerId
     *
     * @param int $websiteId
     * @return \Lof\TableRateShipping\Api\Data\TableRateShippingMethodInterface
     */
    public function setWebsiteId($websiteId);
    /**
     * Get Website Id
     *
     * @return int|null
     */
    public function getWebsiteId();

    /**
     * Set Des Country Id
     *
     * @param string $countryId
     * @return \Lof\TableRateShipping\Api\Data\TableRateShippingMethodInterface
     */
    public function setDestCountryId($countryId);
    /**
     * Get Des Country Id
     *
     * @return string|null
     */
    public function getDestCountryId();

    /**
     * Set DesRegionId
     *
     * @param int $regionId
     * @return \Lof\TableRateShipping\Api\Data\TableRateShippingMethodInterface
     */
    public function setDestRegionId($regionId);
    /**
     * Get DesRegionId
     *
     * @return int|null
     */
    public function getDestRegionId();

    /**
     * Set DesZip
     *
     * @param string $desZip
     * @return \Lof\TableRateShipping\Api\Data\TableRateShippingMethodInterface
     */
    public function setDestZip($desZip);
    /**
     * Get DesZip
     *
     * @return string|null
     */
    public function getDestZip();

    /**
     * Set DesZipTo
     *
     * @param string $zipTo
     * @return \Lof\TableRateShipping\Api\Data\TableRateShippingMethodInterface
     */
    public function setDestZipTo($zipTo);
    /**
     * Get DesZipTo
     *
     * @return string|null
     */
    public function getDestZipTo();

    /**
     * Set Price
     *
     * @param float $price
     * @return \Lof\TableRateShipping\Api\Data\TableRateShippingMethodInterface
     */
    public function setPrice($price);
    /**
     * Get Price
     *
     * @return float|null
     */
    public function getPrice();

    /**
     * Set Weight From
     *
     * @param float $weight
     * @return \Lof\TableRateShipping\Api\Data\TableRateShippingMethodInterface
     */
    public function setWeightFrom($weight);
    /**
     * Get Weight From
     *
     * @return float|null
     */
    public function getWeightFrom();

    /**
     * Set Weight To
     *
     * @param float $weight
     * @return \Lof\TableRateShipping\Api\Data\TableRateShippingMethodInterface
     */
    public function setWeightTo($weight);
    /**
     * Get Weight To
     *
     * @return float|null
     */
    public function getWeightTo();
    /**
     * Set Shipping method Id
     *
     * @param int $shippingMethodId
     * @return \Lof\TableRateShipping\Api\Data\TableRateShippingMethodInterface
     */
    public function setShippingMethodId($shippingMethodId);
    /**
     * Get Shipping method Id
     *
     * @return int|null
     */
    public function getShippingMethodId();
    /**
     * Set Free Shipping
     *
     * @param int $freeShipping
     * @return \Lof\TableRateShipping\Api\Data\TableRateShippingMethodInterface
     */
    public function setFreeShipping($freeShipping);
    /**
     * Get Free Shipping
     *
     * @return int|null
     */
    public function getFreeShipping();

    /**
     * Get Method Name
     *
     * @return string|null
     */
    public function getMethodName();

    /**
     * Set Entity ID
     *
     * @param string $name
     * @return \Lof\TableRateShipping\Api\Data\TableRateShippingMethodInterface
     */
    public function setMethodName($name);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Lof\TableRateShipping\Api\Data\TableRateShippingExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Lof\TableRateShipping\Api\Data\TableRateShippingExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Lof\TableRateShipping\Api\Data\TableRateShippingExtensionInterface $extensionAttributes
    );
}
