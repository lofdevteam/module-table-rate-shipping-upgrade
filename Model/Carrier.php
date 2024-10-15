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

use Magento\Catalog\Model\ProductFactory;
use Magento\Checkout\Model\Session;
use Magento\Framework\Session\SessionManager;
use Magento\Framework\Unserialize\Unserialize;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\Error;
use Magento\Quote\Model\Quote\Item\OptionFactory;
use Magento\Shipping\Model\Rate\Result;

class Carrier extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements
    \Magento\Shipping\Model\Carrier\CarrierInterface
{
    /**
     * Code of the carrier.
     *
     * @var string
     */
    const CODE = 'loftablerateshipping';

    /**
     * Code of the carrier.
     *
     * @var string
     */
    const SEPARATOR = '~';

    /**
     * Code of the carrier.
     *
     * @var string
     */
    protected $_code = self::CODE;

    /**
     * Rate request data.
     *
     * @var \Magento\Quote\Model\Quote\Address\RateRequest|null
     */
    protected $_request;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

     /**
     * @var \Magento\Catalog\Model\ResourceModel\Product
     */
    protected $_resourceProduct;

    /**
     * Rate result data.
     *
     * @var Result|null
     */
    protected $_result;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var array
     */
    protected $_errors = [];

    /**
     * @var bool
     */
    protected $_isFixed = true;

    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    protected $_rateResultFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    protected $_rateMethodFactory;

    /**
     * Raw rate request data.
     *
     * @var \Magento\Framework\DataObject|null
     */
    protected $_rawRequest = null;

    /**
     * @var \Lof\TableRateShipping\Model\ShippingmethodFactory
     */
    protected $_mpshippingMethod;

    /**
     * @var \Lof\TableRateShipping\Model\ResourceModel\Shippingmethod
     */
    protected $_mpshippingMethodResource;

    /**
     * @var \Magento\Framework\Session\SessionManager
     */
    protected $_coreSession;

    /**
     * @var OptionFactory
     */
    protected $_itemOptionModel;

    /**
     * @var ProductFactory
     */
    protected $_mpProductFactory;

    /**
     * @var ShippingFactory
     */
    protected $_mpShippingModel;

    /**
     * @var Unserialize
     */
    protected $_unserialize;

    /**
    * @var \Magento\Framework\Data\CollectionFactory
    */
    protected $collectionFactory;

    /**
    * @var Session
    */
    protected $checkoutSession;

    /**
    * @var Shipping
    */
    protected $shippingModel;

    /**
     * @var mixed
     */
    protected $result;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param ProductFactory $productFactory
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param ShippingmethodFactory $shippingmethodFactory
     * @param SessionManager $coreSession
     * @param OptionFactory $itemOptionModel
     * @param ShippingFactory $mpshippingModel
     * @param \Lof\TableRateShipping\Model\ResourceModel\Shippingmethod $_mpshippingMethodResource
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        ProductFactory $productFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        ShippingmethodFactory $shippingmethodFactory,
        SessionManager $coreSession,
        OptionFactory $itemOptionModel,
        ShippingFactory $mpshippingModel,
        Session $checkoutSession,
        Unserialize $unserialize,
        \Magento\Framework\Data\CollectionFactory $collectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product $resourceProduct,
        \Lof\TableRateShipping\Model\ResourceModel\Shippingmethod $_mpshippingMethodResource,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->_productFactory = $productFactory;
        $this->_objectManager = $objectManager;
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->_mpshippingMethod = $shippingmethodFactory;
        $this->_mpshippingMethodResource = $_mpshippingMethodResource;
        $this->_coreSession = $coreSession;
        $this->_itemOptionModel = $itemOptionModel;
        $this->_mpShippingModel = $mpshippingModel;
        $this->_unserialize = $unserialize;
        $this->checkoutSession = $checkoutSession;
        $this->_resourceProduct = $resourceProduct;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * check min ship price is allow free ship
     * @param float|int $min_ship_price
     * @return boolean
     */
    public function allowFreeShipZeroPrice($min_ship_price)
    {
        $allow_zero_price = $this->getConfigData("allow_free_ship_zero_price");
        $flag = (($min_ship_price > 0) || ($min_ship_price == 0 && $allow_zero_price)) ? true: false;
        if ($this->isDisableFreeShipping()) {
            $flag = false;
        }
        return $flag;
    }

    /**
     * check is disable free shipping for this method or not?
     * @return boolean
     */
    public function isDisableFreeShipping()
    {
        $disable_free_shipping = $this->getConfigData("disable_free_shipping");
        return $disable_free_shipping ? true: false;
    }

    /**
     * Collect and get rates.
     *
     * @param RateRequest $request
     *
     * @return \Magento\Quote\Model\Quote\Address\RateResult\Error|bool|Result
     */
    public function collectRates(RateRequest $request)
    {
        $this->setRequest($request);
        $shippingpricedetail = $this->getShippingPricedetail($this->_rawRequest);
        return $shippingpricedetail;
    }

    /**
     * @param \Magento\Framework\DataObject|null $request
     * @return $this
     * @api
     */
    public function setRawRequest($request)
    {
        $this->_rawRequest = $request;

        return $this;
    }

    /**
     * Prepare and set request to this instance.
     *
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     *
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function setRequest(\Magento\Quote\Model\Quote\Address\RateRequest $request)
    {
        $this->_request = $request;
        $requestData = new \Magento\Framework\DataObject();
        //$product = $this->_productFactory->create();
        //$mpassignproductId = 0;
        $shippingdetail = [];
        $partner = 0;
        //$handling = 0;
        foreach ($request->getAllItems() as $item) {
            if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
                continue;
            }

            $weight = $this->calculateWeightForProduct($item);
            if (empty($shippingdetail)) {
                array_push(
                    $shippingdetail,
                    [
                        'seller_id' => $partner,
                        'items_weight' => $weight,
                        'product_name' => $item->getName(),
                        'item_id' => $item->getId(),
                    ]
                );
            } else {
                $shipinfoflag = true;
                $index = 0;
                foreach ($shippingdetail as $itemship) {
                    if ($itemship['seller_id'] == $partner) {
                        $itemship['items_weight'] = $itemship['items_weight'] + $weight;
                        $itemship['product_name'] = $itemship['product_name'] . ',' . $item->getName();
                        $itemship['item_id'] = $itemship['item_id'] . ',' . $item->getId();
                        $shippingdetail[$index] = $itemship;
                        $shipinfoflag = false;
                    }
                    ++$index;
                }
                if ($shipinfoflag == true) {
                    array_push(
                        $shippingdetail,
                        [
                            'seller_id' => $partner,
                            'items_weight' => $weight,
                            'product_name' => $item->getName(),
                            'item_id' => $item->getId(),
                        ]
                    );
                }
            }
        }

        if ($request->getShippingDetails()) {
            $shippingdetail = $request->getShippingDetails();
        }
        $requestData->setShippingDetails($shippingdetail);

        $requestData->setDestCountryId($request->getDestCountryId());
        $requestData->setDestRegionCode($request->getDestRegionCode());
        $requestData->setDestRegionId($request->getDestRegionId());

        if ($request->getDestPostcode()) {
            //$requestData->setDestPostal(str_replace('-', '', $request->getDestPostcode()));
            $requestData->setDestPostal($request->getDestPostcode());
        }

        $this->setRawRequest($requestData);

        return $this;
    }

    /**
     * Calculate the rate according to Tabel Rate shipping defined by the sellers.
     *
     * @return Result|mixed|string
     */
    public function getShippingPricedetail(\Magento\Framework\DataObject $request)
    {
        $requestData = $request;
        $submethod = [];
        $shippinginfo = [];
        $msg = '';
        $handling = 0;
        $totalPriceArr = [];
        $totalCostArr = [];
        $debugData = [];
        //$existsMethodRates = [];
        $flag = false;
        $check = false;
        $returnError = false;
        //$shippingCollection = $this->_mpShippingModel->create()->getCollection()->addFieldToSelect('*');
        $quote = $this->checkoutSession->getQuote();
        $quoteData = $quote->getData();
        if(isset($quoteData['subtotal'])){
            $subtotal = floatval($quoteData['subtotal']);
            foreach ($requestData->getShippingDetails() as $shipdetail) {
                $thisMsg = false;
                $priceArr = [];
                $costArr = [];
                $shipdetail["seller_id"] = 0;
                $foundShippingRates = $this->getShippingPriceRates($shipdetail, $requestData, $subtotal);
                foreach ($foundShippingRates as $shipping) {
                    //Get Shipping Price
                    $price =  floatval($shipping->getPrice());
                    $free_ship_price = floatval($shipping->getFreeShipping());
                    $is_allow_free_ship = $this->allowFreeShipZeroPrice($free_ship_price);
                    if ($is_allow_free_ship && ($this->_request->getFreeShipping() == true || $subtotal >= $free_ship_price)) {
                        $price = 0;
                    }

                    if (!$shipping->getData()) {
                        continue;
                    }

                    $returnRateArr = $this->getPriceArrForRate($shipping, $price);
                    $priceArr = $returnRateArr['price'];
                    $costArr = $returnRateArr['cost'];

                    if (!empty($totalPriceArr)) {
                        foreach ($priceArr as $methodId => $_price) {
                            // Calculate price
                            if (!array_key_exists($methodId, $totalPriceArr)) {
                                $check = true;
                                // phpcs:disable Magento2.Performance.ForeachArrayMerge.ForeachArrayMerge
                                $totalPriceArr = array_merge($totalPriceArr, $priceArr);
                                $priceArr = $totalPriceArr;
                            } else {
                                $thisMsg = true;
                                unset($priceArr[$methodId]);
                            }

                            // Calculate cost
                            if (!array_key_exists($methodId, $totalCostArr)) {
                                $totalCostArr = array_merge($totalCostArr, $costArr);
                                $costArr = $totalCostArr;
                            } else {
                                unset($costArr[$methodId]);
                            }
                            $flag = $check == true ? false : true;
                        }
                    } else {
                        $totalPriceArr = $priceArr;
                        $totalCostArr = $costArr;
                    }
                    if (empty($priceArr)) {
                        $totalPriceArr = [];
                        $totalCostArr = [];
                        $flag = true;
                    }
                    if ($flag) {
                        if ($thisMsg) {
                            $msg = $this->getErrorMsg($msg, $shipdetail);
                        }
                        $returnError = true;
                        $debugData['result'] = ['error' => 1, 'errormsg' => $msg];
                    }
                    $submethod = $this->getSubMethodsForRate($priceArr, $costArr);
                    $handling += $price;
                    array_push(
                        $shippinginfo,
                        [
                            'methodcode' => $this->_code,
                            'shipping_ammount' => $price,
                            'product_name' => $shipdetail['product_name'],
                            'submethod' => $submethod,
                            'item_ids' => $shipdetail['item_id'],
                        ]
                    );

                }
                if ($returnError) {
                    return $this->_parseXmlResponse($debugData);
                }
                //echo "<pre>"; print_r($shippinginfo);die();
                $totalpric = ['totalprice' => $totalPriceArr, 'costarr' => $totalCostArr];
                $result = ['handlingfee' => $totalpric, 'shippinginfo' => $shippinginfo, 'error' => 0];
                /** @phpstan-ignore-next-line */
                $shippingAll = $this->_coreSession->getShippingInfo();
                $shippingAll[$this->_code] = $result['shippinginfo'];
                /** @phpstan-ignore-next-line */
                $this->_coreSession->setShippingInfo($shippingAll);
                return $this->_parseXmlResponse($totalpric);
            }
        }
        return "";
    }

    /**
     * @return array
     */
    public function getAllowedMethods()
    {
        return ['loftablerateshipping' => $this->getConfigData('name')];
    }

    /**
     * get Shipping method name
     * @param int $shipMethodId
     * @return mixed
     */
    public function getShipMethodNameById($shipMethodId)
    {
        $methodName = '';
        $shippingMethodModel = $this->_mpshippingMethod->create();
        $this->_mpshippingMethodResource->load($shippingMethodModel, $shipMethodId);
        $methodName = $shippingMethodModel->getMethodName();
        return $methodName;
    }

    /**
     * @param mixed|object|array $response
     * @return Error|Result
     */
    protected function _parseXmlResponse($response)
    {
        $result = $this->_rateResultFactory->create();
        if (array_key_exists('result', $response) && $response['result']['error'] !== '') {
            $this->_errors[$this->_code] = $response['result']['errormsg'];
            $errors = explode('<br>', $response['result']['errormsg']);
            $error = $this->_rateErrorFactory->create();
            $error->setCarrier($this->_code);
            $error->setCarrierTitle($this->getConfigData('title'));
            foreach ($errors as $value) {
                $errorMsg[] = $value;
            }
            $error->setErrorMessage($errorMsg);
            return $error;
        } else {
            $totalPriceArr = $response['totalprice'];
            $costArr = $response['costarr'];
            if ($this->getConfigData('show_min')) {
                $totalPriceArr = $this->getMinArrayItem($totalPriceArr);
            }

            foreach ($totalPriceArr as $key => $price) {
                if (isset($price['method_id']) && $price['method_id']) {
                    $rate = $this->_rateMethodFactory->create();
                    $rate->setCarrier($this->_code);
                    $rate->setSellerId(0);
                    $rate->setCarrierTitle($this->getConfigData('title'));
                    $rate->setMethod($this->_code . $price['method_id']);
                    $rate->setMethodTitle($price['method_title']);
                    $rate->setCost($costArr[$key]['cost']);
                    $rate->setPrice($price['price']);
                    $result->append($rate);
                }
            }
        }

        return $result;
    }

    /**
     *  Returns maximum in array
     *  @param array|mixed $array
     *  @return array|mixed
     */
    protected function getMinArrayItem($array = [])
    {
        if (count($array)) {
            $minKey = array_key_first($array);
            $min = $array[$minKey]["price"];
            $minItem = $array[$minKey];

            foreach ($array as $key => $_item) {
                if ($min > $_item["price"]) {
                    $min = $_item["price"];
                    $minItem = $_item;
                    $minKey = $key;
                }
            }

            return [$minKey => $minItem ];
        } else {
            return $array;
        }
    }

    /**
     * calculate Weight For Product
     * @param object|mixed $item
     * @return int|float
     */
    public function calculateWeightForProduct($item)
    {
        $childWeight = 0;
        $weight = 0;
        if ($item->getHasChildren()) {
            $_product = $this->getProductById($item->getProductId());
            if ($_product->getTypeId() == 'bundle') {
                foreach ($item->getChildren() as $child) {
                    $childProduct = $this->getProductById($child->getProductId());
                    $productWeight = $childProduct->getWeight();
                    $childWeight += $productWeight * $child->getQty();
                }
                $weight = $childWeight * $item->getQty();
            } elseif ($_product->getTypeId() == 'configurable') {
                foreach ($item->getChildren() as $child) {
                    $childProduct = $this->getProductById($child->getProductId());
                    $productWeight = $childProduct->getWeight();
                    $weight = $productWeight * $item->getQty();
                }
            }
        } else {
            $childProduct = $this->getProductById($item->getProductId());
            $productWeight = $childProduct->getWeight();
            $weight = $productWeight * $item->getQty();
        }
        return $weight;
    }

    /**
     * get Shipping collection According To Details
     * @param string $countryId
     * @param string|int $regionId
     * @param string|int $postalCode
     * @param float $weight
     * @param int|null $sellerId
     * @param float|int $subtotal
     * @return \Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    public function getShippingcollectionAccordingToDetails($countryId, $regionId, $postalCode, $weight, $sellerId = 0,  $subtotal = 0)
    {
        $filterPartner = ["eq" => (int)$sellerId];
        if($this->getConfigData('allowadmin')){
            $filterPartner = ["in" => [(int)$sellerId, 0]];
        }
        $shipping = $this->_mpShippingModel->create()
                    ->getCollection()
                    ->addFieldToFilter('dest_country_id', ['eq' => $countryId])
                    ->addFieldToFilter('dest_region_id', [
                        ['eq' => '*'],
                        ['eq' => ''],
                        ['eq' => !empty($regionId) ? strtoupper($regionId) : $regionId]
                    ])
                    ->addFieldToFilter('dest_zip', [
                        ['lteq' => $postalCode],
                        ['eq' => '*'],
                        ['eq' => !empty($postalCode) ? strtoupper($postalCode) : $postalCode]
                    ])
                    ->addFieldToFilter('dest_zip_to', [
                        ['gteq' => $postalCode],
                        ['eq' => !empty($postalCode) ? strtoupper($postalCode) : $postalCode],
                        ['eq' => '*'],
                        ['eq' => '']
                    ])
                    ->addFieldToFilter('weight_from', ['lteq' => $weight])
                    ->addFieldToFilter('weight_to', ['gteq' => $weight])
                    ->addFieldToFilter('partner_id', $filterPartner)
                    ->addFieldToFilter(
                        'cart_total',
                        [
                            ['lteq' => (float)$subtotal],
                            ['null' => true],
                            ['eq' => '']
                        ]
                    );
        return $shipping;
    }

    /**
     * @param array|mixed $shipdetail
     * @param mixed|object $requestData
     * @param float|int $subtotal
     * @return \Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|null
     */
    public function getShippingPriceRates($shipdetail, $requestData, $subtotal = 0)
    {
        $shipping = $this->getShippingcollectionAccordingToDetails(
            $requestData->getDestCountryId(),
            $requestData->getDestRegionCode(),
            $requestData->getDestPostal(),
            $shipdetail['items_weight'],
            $shipdetail['seller_id'],
            $subtotal
        );
        return $shipping;
    }

    /**
     * @param mixed|object $shipping
     * @param float|int $shipping_price
     * @return array|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPriceArrForRate($shipping, $shipping_price = 0)
    {
        $priceArr = [];
        $costArr = [];
        $shippingId = $shipping->getId();
        $shipMethodId = $shipping->getShippingMethodId();
        if ($shipMethodId) {
            $shipMethodName = $this->getShipMethodNameById($shipMethodId);
        } else {
            $shipMethodName = $this->getConfigData('title');
        }
        $priceArr[$shippingId] = [
            'method_id' => $shippingId,
            'price' => floatval($shipping_price),
            'method_title' => $shipMethodName
        ];
        $costArr[$shippingId] = [
            'method_id' => $shippingId,
            'cost' => $shipping->getCost() ? floatval($shipping->getCost()) : $shipping_price,
            'method_title' => $shipMethodName
        ];
        return [
            'price' => $priceArr,
            'cost' => $costArr
        ];
    }

    /**
     * getSubMethodsForRate
     * @param array|mixed $priceArr
     * @param array|mixed $costArr
     */
    public function getSubMethodsForRate($priceArr, $costArr)
    {
        $submethod = [];
        if (!empty($priceArr)) {
            foreach ($priceArr as $index => $price) {
                $submethod[$index] = [
                    'method' => $index . ' (' . $this->getConfigData('title') . ')',
                    'cost' => isset($costArr[$index]) ? $costArr[$index] : $price,
                    'base_amount' => $price,
                    'error' => 0,
                ];
            }
        }
        return $submethod;
    }

    /**
     * @param string $msg
     * @param mixed|array $shipdetail
     * @return \Magento\Framework\Phrase|string
     */
    public function getErrorMsg($msg, $shipdetail)
    {
        $thisMsg = __(
            'Product %1 do not provide shipping service to your location.',
            $shipdetail['product_name']
        );
        if ($msg=='') {
            $msg = $thisMsg;
        } else {
            $msg = $msg . "<br>" . $thisMsg;
        }
        return $msg;
    }

    /**
     * Get product by id
     * @param int $productId
     * @return \Magento\Catalog\Model\Product
     */
    public function getProductById($productId)
    {
        $product = $this->_productFactory->create();
        $this->_resourceProduct->load($product, $productId);
        return $product;
    }
}
