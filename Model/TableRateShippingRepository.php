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
declare(strict_types=1);

namespace Lof\TableRateShipping\Model;

use Lof\TableRateShipping\Api\Data\TableRateShippingInterfaceFactory;
use Lof\TableRateShipping\Api\Data\TableRateShippingSearchResultsInterfaceFactory;
use Lof\TableRateShipping\Api\TableRateShippingRepositoryInterface;
use Lof\TableRateShipping\Model\ResourceModel\Shipping as ResourceTableRateShipping;
use Lof\TableRateShipping\Model\ShippingmethodFactory;
use Lof\TableRateShipping\Model\ResourceModel\Shippingmethod as ResourceShippingmethod;
use Lof\TableRateShipping\Model\ResourceModel\Shipping\CollectionFactory as TableRateShippingCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;

class TableRateShippingRepository implements TableRateShippingRepositoryInterface
{

    /**
     * @var ResourceTableRateShipping
     */
    protected $resource;

    /**
     * @var ShippingFactory
     */
    protected $tableRateShippingFactory;

    /**
     * @var TableRateShippingCollectionFactory
     */
    protected $tableRateShippingCollectionFactory;

    /**
     * @var TableRateShippingSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var TableRateShippingInterfaceFactory
     */
    protected $dataTableRateShippingFactory;

    /**
     * @var JoinProcessorInterface
     */
    protected $extensionAttributesJoinProcessor;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var ExtensibleDataObjectConverter
     */
    protected $extensibleDataObjectConverter;
    /**
     * @var ShippingmethodFactory
     */
    private $shippingMethodFactory;

    /**
     * @var ResourceShippingmethod
     */
    protected $resourceShippingMethod;

    /**
     * @param ResourceTableRateShipping $resource
     * @param ShippingFactory $tableRateShippingFactory
     * @param TableRateShippingInterfaceFactory $dataTableRateShippingFactory
     * @param TableRateShippingCollectionFactory $tableRateShippingCollectionFactory
     * @param TableRateShippingSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     * @param ShippingmethodFactory $shippingMethodFactory
     * @param ResourceShippingmethod $resourceShippingMethod
     */
    public function __construct(
        ResourceTableRateShipping $resource,
        ShippingFactory $tableRateShippingFactory,
        TableRateShippingInterfaceFactory $dataTableRateShippingFactory,
        TableRateShippingCollectionFactory $tableRateShippingCollectionFactory,
        TableRateShippingSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        ShippingmethodFactory $shippingMethodFactory,
        ResourceShippingmethod $resourceShippingMethod
    ) {
        $this->resource = $resource;
        $this->tableRateShippingFactory = $tableRateShippingFactory;
        $this->tableRateShippingCollectionFactory = $tableRateShippingCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataTableRateShippingFactory = $dataTableRateShippingFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
        $this->shippingMethodFactory = $shippingMethodFactory;
        $this->resourceShippingMethod = $resourceShippingMethod;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Lof\TableRateShipping\Api\Data\TableRateShippingInterface $tableRateShipping
    ) {
        $shippingMethod = $this->shippingMethodFactory->create();
        $data['method_name'] = $tableRateShipping->getMethodName();
        $data['partner_id'] = 0;
        $shippingMethod->setData($data);
        $this->resourceShippingMethod->save($shippingMethod);
        //$shippingMethod->save();
        $tableRateShipping->setShippingMethodId($shippingMethod->getEntityId());
        $tableRateShippingData = $this->extensibleDataObjectConverter->toNestedArray(
            $tableRateShipping,
            [],
            \Lof\TableRateShipping\Api\Data\TableRateShippingInterface::class
        );

        $tableRateShippingModel = $this->tableRateShippingFactory->create()->setData($tableRateShippingData);

        try {
            $this->resource->save($tableRateShippingModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the tableRateShipping: %1',
                $exception->getMessage()
            ));
        }
        return $tableRateShippingModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($tableRateShippingId)
    {
        $tableRateShipping = $this->getShippingmethodById($tableRateShippingId);
        if (!$tableRateShipping->getId()) {
            throw new NoSuchEntityException(__('TableRateShipping with id "%1" does not exist.', $tableRateShippingId));
        }
        $method = $this->shippingMethodFactory->create();
        $this->resourceShippingMethod->load($method, $tableRateShipping->getShippingMethodId());
        $tableRateShipping->setMethodName($method->getMethodName());
        return $tableRateShipping->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->tableRateShippingCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Lof\TableRateShipping\Api\Data\TableRateShippingInterface::class
        );

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = [];
        foreach ($collection as $model) {
            $method = $this->getShippingmethodById($model->getShippingMethodId());
            $model->setMethodName($method->getMethodName());
            $items[] = $model->getDataModel();
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Lof\TableRateShipping\Api\Data\TableRateShippingInterface $tableRateShipping
    ) {
        try {
            $method = $this->getShippingmethodById($tableRateShipping->getShippingMethodId());
            $this->resourceShippingMethod->delete($method);
            $tableRateShippingModel = $this->tableRateShippingFactory->create();
            $this->resource->load($tableRateShippingModel, $tableRateShipping->getLofshippingId());
            $this->resource->delete($tableRateShippingModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the TableRateShipping: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($tableRateShippingId)
    {
        return $this->delete($this->get($tableRateShippingId));
    }

    /**
     * get shipping method by id
     * @param int $methodId
     * @return \Lof\TableRateShipping\Model\Shippingmethod
     */
    public function getShippingmethodById($methodId)
    {
        $method = $this->shippingMethodFactory->create();
        $this->resourceShippingMethod->load($method, $methodId);
        return $method;
    }
}
