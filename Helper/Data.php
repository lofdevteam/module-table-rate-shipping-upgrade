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

namespace Lof\TableRateShipping\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     */

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\Session $customerSession
    ) {
        parent::__construct($context);
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_customerSession = $customerSession;
    }

    /**
     * get shipping is enabled or not for system config.
     * @return bool
     */
    public function getTableRateShippingEnabled()
    {
        return (bool)$this->_scopeConfig->getValue(
            'carriers/loftablerateshipping/active',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * get multi shipping is enabled or not for system config.
     * @return bool
    */
    public function getMpmultishippingEnabled()
    {
        return (bool)$this->_scopeConfig->getValue(
            'carriers/mp_multi_shipping/active',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * get table rate shipping title from system config.
     * @return mixed
     */
    public function getTableRateShippingTitle()
    {
        return $this->_scopeConfig->getValue(
            'carriers/loftablerateshipping/title',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * get table rate shipping name from system config.
     * @return mixed
     */
    public function getTableRateShippingName()
    {
        return $this->_scopeConfig->getValue(
            'carriers/loftablerateshipping/name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * get table rate shipping allow admin settings from system config.
     * @return mixed
     */
    public function getTableRateShippingAllowadmin()
    {
        return $this->_scopeConfig->getValue(
            'carriers/loftablerateshipping/allowadmin',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * get is disable free shipping
     *
     * @return bool|int
     */
    public function getIsDisableFreeShipping()
    {
        return (bool)$this->_scopeConfig->getValue(
            'carriers/loftablerateshipping/disable_free_shipping',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * get allow free shipping for zero price
     *
     * @return bool
     */
    public function getAllowFreeShipForZeroPrice()
    {
        return (bool)$this->_scopeConfig->getValue(
            'carriers/loftablerateshipping/allow_free_ship_zero_price',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );//allow_free_ship_zero_price
    }

    /**
     * get customer id from customer session.
     * @return int|string
     */
    public function getPartnerId()
    {
        $partnerId = $this->_customerSession->getCustomerId();
        return $partnerId;
    }
}
