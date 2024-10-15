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

namespace Lof\TableRateShipping\Block\Shipping;

use Magento\Catalog\Block\Product\AbstractProduct;
use Lof\TableRateShipping\Model\ShippingmethodFactory;
use Magento\Directory\Model\ResourceModel\Country;
use Lof\TableRateShipping\Model\ShippingFactory;

class Shipping extends AbstractProduct
{
    /**
     * @var \Magento\Framework\Data\Helper\PostHelper
     */
    protected $_postDataHelper;
    /**
     * @var \Magento\Framework\Url\Helper\Data
     */
    protected $_urlHelper;
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_session;
    /**
     * @var ShippingmethodFactory
     */
    protected $_mpshippingMethod;
    /**
     * @var \Magento\Directory\Model\ResourceModel\Country\CollectionFactory
     */
    protected $_countryCollectionFactory;
    /**
     * @var ShippingFactory
     */
    protected $_mpshippingModel;

    /**
     * @var object|mixed
     */
    protected $request;

    /**
     * @param \Magento\Catalog\Block\Product\Context    $context
     * @param \Magento\Framework\Data\Helper\PostHelper $postDataHelper
     * @param \Magento\Framework\Url\Helper\Data        $urlHelper
     * @param \Magento\Customer\Model\Session           $session
     * @param ShippingmethodFactory                   $shippingmethodFactory
     * @param Country\CollectionFactory                 $countryCollectionFactory
     * @param ShippingFactory                         $mpshippingModel
     * @param array                                     $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Customer\Model\Session $session,
        ShippingmethodFactory $shippingmethodFactory,
        Country\CollectionFactory $countryCollectionFactory,
        ShippingFactory $mpshippingModel,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_postDataHelper = $postDataHelper;
        $this->_urlHelper = $urlHelper;
        $this->_session = $session;
        $this->request =  $context->getRequest();
        $this->_mpshippingMethod = $shippingmethodFactory;
        $this->_countryCollectionFactory = $countryCollectionFactory;
        $this->_mpshippingModel = $mpshippingModel;
    }

    /**
     * Get shipping id
     *
     * @return string|int
     */
    public function getShippingId()
    {
        $path = trim($this->request->getPathInfo(), '/');
        $params = explode('/', $path);
        return end($params);
    }

    /**
     * @return int
     */
    public function getCustomerId()
    {
        return $this->_session->getCustomerId();
    }

    /**
     * get shipping
     * @param int $shipping_id
     * @return object|mixed
     */
    public function getShipping($shipping_id)
    {
         $querydata = $this->_mpshippingModel->create()
            ->getCollection()
            ->addFieldToFilter(
                'lofshipping_id', $shipping_id
            );
        return $querydata;
    }

    /**
     * @param  int|null $partnerId
     * @return object|mixed
     */
    public function getShippingCollection($partnerId = null)
    {
        $querydata = $this->_mpshippingModel->create()
            ->getCollection()
            ->addFieldToFilter(
                'partner_id',
                ['eq' => $partnerId]
            );
        return $querydata;
    }

    /**
     * get shipping method collection
     *
     * @return object|mixed
     */
    public function getShippingMethodCollection()
    {
        $shippingMethodCollection = $this->_mpshippingMethod
            ->create()
            ->getCollection();
        return $shippingMethodCollection;
    }

    /**
     * get shipping method collection
     *
     * @return object|mixed
     */
    public function getShippingMethod()
    {
        $shippingMethodCollection = $this->_mpshippingMethod
            ->create();
        return $shippingMethodCollection;
    }

    /**
     * get getShippingforShippingMethod
     * @param int|string $methodId
     * @param int|string $partnerId
     * @return object|mixed
     */
    public function getShippingforShippingMethod($methodId, $partnerId)
    {
        $querydata = $this->_mpshippingModel
            ->create()
            ->getCollection()
            ->addFieldToFilter(
                'shipping_method_id',
                ['eq' => $methodId]
            )
            ->addFieldToFilter(
                'partner_id',
                ['eq' => $partnerId]
            );
        return $querydata;
    }

    /**
     * get getShippingforShippingMethod
     * @param int|string $shippingMethodId
     * @return string
     */
    public function getShippingMethodName($shippingMethodId)
    {
        $methodName = '';
        $shippingMethodModel = $this->_mpshippingMethod->create()
            ->getCollection()
            ->addFieldToFilter('entity_id', $shippingMethodId);
        foreach ($shippingMethodModel as $shippingMethod) {
            $methodName = $shippingMethod->getMethodName();
        }
        return $methodName;
    }

    /**
     * get getCountryOptionArray
     * @return mixed|array
     */
    public function getCountryOptionArray()
    {
        $options = $this->getCountryCollection()
            ->setForegroundCountries($this->getTopDestinations())
            ->toOptionArray();
        $options[0]['label'] = 'Please select Country';

        return $options;
    }

    /**
     * get getCountryCollection
     * @return mixed|object
     */
    public function getCountryCollection()
    {
        $collection = $this->_countryCollectionFactory
            ->create()
            ->loadByStore();
        return $collection;
    }

    /**
     * Retrieve list of top destinations countries.
     *
     * @return array
     */
    protected function getTopDestinations()
    {
        $destinations = (string) $this->_scopeConfig->getValue(
            'general/country/destinations',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return !empty($destinations) ? explode(',', $destinations) : [];
    }
}
