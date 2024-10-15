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

interface TableRateShippingMethodInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ENTITY_ID = 'entity_id';
    const METHOD_NAME = 'method_name';
    const PARTNER_ID = 'partner_id';
    /**#@-*/

    /**
     * Get Entity ID
     *
     * @return int|null
     */
    public function getEntityId();
    /**
     * Set Entity ID
     *
     * @param int $id
     * @return \Lof\TableRateShipping\Api\Data\TableRateShippingMethodInterface
     */
    public function setEntityId($id);

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

}
