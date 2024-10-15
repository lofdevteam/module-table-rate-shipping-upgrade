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

use Lof\TableRateShipping\Api\Data\TableRateShippingInterface;
use Lof\TableRateShipping\Api\Data\TableRateShippingInterfaceFactory;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Api\DataObjectHelper;

class Shipping extends AbstractModel implements IdentityInterface
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
     * @var DataObjectHelper
     */
    private $dataObjectHelper;
    /**
     * @var TableRateShippingInterfaceFactory
     */
    private $tableRateShippingInterfaceFactory;

    /**
     * Shipping constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param DataObjectHelper $dataObjectHelper
     * @param TableRateShippingInterfaceFactory $tableRateShippingInterfaceFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        DataObjectHelper $dataObjectHelper,
        TableRateShippingInterfaceFactory $tableRateShippingInterfaceFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {

        $this->dataObjectHelper = $dataObjectHelper;
        $this->tableRateShippingInterfaceFactory = $tableRateShippingInterfaceFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Lof\TableRateShipping\Model\ResourceModel\Shipping::class);
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getLofshippingId()];
    }

    /**
     * @return TableRateShippingInterface
     */
    public function getDataModel()
    {
        $shippingData = $this->getData();

        $shippingDataObject = $this->tableRateShippingInterfaceFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $shippingDataObject,
            $shippingData,
            TableRateShippingInterface::class
        );
        return $shippingDataObject;
    }
}
