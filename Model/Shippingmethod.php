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

namespace Lof\TableRateShipping\Model;

use Lof\TableRateShipping\Api\Data\TableRateShippingMethodInterface;
use Magento\Framework\DataObject\IdentityInterface;
use \Magento\Framework\Model\AbstractModel;

class Shippingmethod extends AbstractModel implements TableRateShippingMethodInterface, IdentityInterface
{
    /**
     * CMS page cache tag
     */
    const CACHE_TAG = 'loftablerateshipping';
    /**
     * @var string
     */
    protected $_cacheTag = 'loftablerateshipping';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'loftablerateshipping';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Lof\TableRateShipping\Model\ResourceModel\Shippingmethod::class);
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getEntityId()];
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getEntityId()
    {
        return $this->getData(self::ENTITY_ID);
    }

    /**
     * @param int $id
     * @return TableRateShippingMethodInterface|Shippingmethod
     */
    public function setEntityId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }

    /**
     * @return array|mixed|string|null
     */
    public function getMethodName()
    {
        return $this->getData(self::METHOD_NAME);
    }

    /**
     * @param string $name
     * @return TableRateShippingMethodInterface|Shippingmethod
     */
    public function setMethodName($name)
    {
        return $this->setData(self::METHOD_NAME, $name);
    }

    /**
     * @param int $partnerId
     * @return TableRateShippingMethodInterface|Shippingmethod
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
        return $this->getData(self::PARTNER_ID);
    }
}
