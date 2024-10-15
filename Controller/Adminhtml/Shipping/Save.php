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

namespace Lof\TableRateShipping\Controller\Adminhtml\Shipping;

use Lof\TableRateShipping\Model\ShippingFactory;
use Lof\TableRateShipping\Model\ShippingmethodFactory;
use Lof\TableRateShipping\Model\ResourceModel\Shippingmethod;
use Lof\TableRateShipping\Model\ResourceModel\Shipping as ShippingResource;
use Magento\Backend\App\Action;
use Magento\MediaStorage\Model\File\UploaderFactory;

class Save extends \Magento\Backend\App\Action
{
    /**
     * Core registry.
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;
    /**
     * @var ShippingmethodFactory
     */
    protected $_mpshippingMethodFactory;
    /**
     * @var ShippingFactory
     */
    protected $_mpshipping;

    /**
     * @var ShippingResource
     */
    protected $_mpshippingResource;

    /**
     * @var Shippingmethod
     */
    protected $_resource;

    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $_fileUploader;
    /**
     * @var \Magento\Framework\File\Csv
     */
    protected $_csvReader;

    /**
     * @param Action\Context                             $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param ShippingmethodFactory $shippingmethodFactory
     * @param ShippingFactory $mpshipping
     * @param UploaderFactory $fileUploader
     * @param \Magento\Framework\File\Csv $csvReader
     * @param Shippingmethod $resource
     * @param ShippingResource $mpshippingResource
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        ShippingmethodFactory $shippingmethodFactory,
        ShippingFactory $mpshipping,
        UploaderFactory $fileUploader,
        \Magento\Framework\File\Csv $csvReader,
        Shippingmethod $resource,
        ShippingResource $mpshippingResource
    ) {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
        $this->_mpshippingMethodFactory = $shippingmethodFactory;
        $this->_mpshipping = $mpshipping;
        $this->_fileUploader = $fileUploader;
        $this->_csvReader = $csvReader;
        $this->_resource = $resource;
        $this->_mpshippingResource = $mpshippingResource;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @phpstan-ignore-next-line */
        $data = $this->getRequest()->getPostValue();
        /** @phpstan-ignore-next-line */
        if ($this->getRequest()->isPost()) {
            try {
                /** @phpstan-ignore-next-line */
                $files = $this->getRequest()->getFiles();
                if (count($files->toArray())) {
                    $uploader = $this->_fileUploader->create(
                        ['fileId' => 'import_file']
                    );
                    $fileUpload = $uploader->validateFile();
                    $this->runImportCsvFile($fileUpload);
                } else {
                    $params = $data;
                    $params['dest_region_id'] = isset($params['dest_region_id']) && $params['dest_region_id']?strtoupper($params['dest_region_id']):"";
                    /** @phpstan-ignore-next-line */
                    $params['dest_zip'] = isset($params['dest_zip']) && $params['dest_zip']?strtoupper($params['dest_zip']):"";
                    /** @phpstan-ignore-next-line */
                    $params['dest_zip_to'] = isset($params['dest_zip_to']) && $params['dest_zip_to']?strtoupper($params['dest_zip_to']):"";
                    $partnerid = 0;
                    /** @phpstan-ignore-next-line */
                    $shippingMethodId = $this->calculateShippingMethodId($params['shipping_method']);

                    if ($shippingMethodId==0) {
                        $savedMethod = $this->_mpshippingMethodFactory->create();
                        /** @phpstan-ignore-next-line */
                        $savedMethod->setMethodName($params['shipping_method']);
                        $this->_resource->save($savedMethod);
                        $shippingMethodId = $savedMethod->getEntityId();
                    }

                    $shippingModel = $this->_mpshipping->create();
                    /** @phpstan-ignore-next-line */
                    if (isset($params['lofshipping_id']) && $params['lofshipping_id']) {
                        $id = $params['lofshipping_id'];
                        $this->_mpshippingResource->load($shippingModel, $id);
                        /** @phpstan-ignore-next-line */
                        $partnerid = isset($params["partner_id"]) && $params["partner_id"]?$params["partner_id"]:$shippingModel->getData('partner_id');

                        if ($id != $shippingModel->getId()) {
                            throw new \Magento\Framework\Exception\LocalizedException(__('The wrong shipping is specified.'));
                        }
                        $temp = [
                            'lofshipping_id' => $params['lofshipping_id'],
                            'dest_country_id' => $params['dest_country_id'],
                            'dest_region_id' => $params['dest_region_id'],
                            'dest_zip' => $params['dest_zip'],
                            'dest_zip_to' => $params['dest_zip_to'],
                            'price' => (float)$params['price'],
                            'cost' => (float)$params['cost'],
                            'cart_total' => $params['cart_total']?(float)$params['cart_total']:0.0000,
                            'free_shipping' => $params['free_shipping']?(float)$params['free_shipping']:0.0000,
                            'weight_from' => (float)$params['weight_from'],
                            'weight_to' => (float)$params['weight_to'],
                            'shipping_method_id' => $shippingMethodId,
                            'partner_id' => $partnerid
                        ];
                    } else {
                        /** @phpstan-ignore-next-line */
                        $partnerid = isset($params["partner_id"]) && $params["partner_id"]?$params["partner_id"]:0;
                        $temp = [
                            'dest_country_id' => $params['dest_country_id'],/** @phpstan-ignore-line */
                            'dest_region_id' => $params['dest_region_id'],
                            'dest_zip' => $params['dest_zip'],
                            'dest_zip_to' => $params['dest_zip_to'],
                            'price' => (float)$params['price'],/** @phpstan-ignore-line */
                            'cart_total' => $params['cart_total']?(float)$params['cart_total']:0.0000,/** @phpstan-ignore-line */
                            'free_shipping' => $params['free_shipping']?(float)$params['free_shipping']:0.0000,/** @phpstan-ignore-line */
                            'weight_from' => (float)$params['weight_from'],/** @phpstan-ignore-line */
                            'weight_to' => (float)$params['weight_to'],/** @phpstan-ignore-line */
                            'shipping_method_id' => $shippingMethodId,
                            'partner_id' => $partnerid,
                            'cost' => (float)$params['cost'] /** @phpstan-ignore-line */
                        ];
                    }
                    $shippingCollection = $this->_mpshipping->create()
                        ->getCollection()
                        ->addFieldToFilter('partner_id', $partnerid)
                        ->addFieldToFilter('dest_country_id', $params['dest_country_id'])/** @phpstan-ignore-line */
                        ->addFieldToFilter('dest_region_id', $params['dest_region_id'])
                        ->addFieldToFilter('dest_zip', $params['dest_zip'])
                        ->addFieldToFilter('dest_zip_to', $params['dest_zip_to'])
                        ->addFieldToFilter('weight_from', (float)$params['weight_from'])/** @phpstan-ignore-line */
                        ->addFieldToFilter('weight_to', (float)$params['weight_to'])/** @phpstan-ignore-line */
                        ->addFieldToFilter('cart_total', (float)$params['cart_total'])
                        ->addFieldToFilter('shipping_method_id', $shippingMethodId);

                    $shipping_id = isset($params['lofshipping_id']) ? (int)$params['lofshipping_id'] : 0;
                    if ($shippingCollection->getsize() > 0) {
                        foreach ($shippingCollection as $row) {
                            $dataArray = [
                                'price' => (float)$temp['price'],
                                'free_shipping' => $temp['free_shipping']?(float)$temp['free_shipping']:0.0000
                            ];
                            $_shippingModel = $this->_mpshipping->create();
                            $this->_mpshippingResource->load($_shippingModel, $row->getLofshippingId());
                            $_shippingModel->addData($dataArray);
                            $this->_mpshippingResource->save($_shippingModel);
                        }
                    } else {
                        //$shippingModel = $this->_mpshipping->create();
                        $shippingModel->setData($temp);
                        $this->_mpshippingResource->save($shippingModel);
                        $shipping_id = $shippingModel->getId();
                    }
                    $this->messageManager->addSuccess(__('Your shipping detail has been successfully Saved'));
                    // check if 'Save and Continue'
                    if ($this->getRequest()->getParam('back')) {
                        return $this->resultRedirectFactory->create()->setPath('*/*/edit', ['lofshipping_id' => $shipping_id]);/** @phpstan-ignore-line */
                    }
                }
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        return $this->resultRedirectFactory->create()->setPath('*/*/index');
    }

    /**
     * @inheritdoc
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_TableRateShipping::shipping_save');
    }

    /**
     * run import csv file
     *
     * @param mixed|array $result
     * @return mixed|void
     */
    protected function runImportCsvFile($result = array())
    {
        if (!$result || !$this->_formKeyValidator->validate($this->getRequest())) {
            return $this->resultRedirectFactory->create()->setPath('*/*/index');
        }
        $file = $result['tmp_name'];
        $fileNameArray = explode('.', $result['name']);
        $ext = end($fileNameArray);
        if ($file != '' && $ext == 'csv') {
            $csvFileData = $this->_csvReader->getData($file);
            //$partnerid = 0;
            $count = 0;
            $headingArray = [];
            foreach ($csvFileData as $key => $rowData) {
                if($count == 0){
                    foreach($rowData as $i=>$label){
                        if(!isset($headingArray[$label])){
                            $headingArray[$label] = $i;
                        }
                    }
                    ++$count;
                    continue;
                }

                $temp = [];
                $shipping_method = $this->getRowValue($rowData, $headingArray, "shipping_method");
                $shippingMethodId = $this->calculateShippingMethodId($shipping_method);
                $temp['dest_country_id'] = $this->getRowValue($rowData, $headingArray, "country_code");
                $temp['dest_region_id'] = $this->getRowValue($rowData, $headingArray, "region_id");
                $temp['dest_zip'] = $this->getRowValue($rowData, $headingArray, "zip");
                $temp['dest_zip_to'] = $this->getRowValue($rowData, $headingArray, "zip_to");
                $temp['price'] = (float)$this->getRowValue($rowData, $headingArray, "price");
                $temp['weight_from'] = (float)$this->getRowValue($rowData, $headingArray, "weight_from");
                $temp['weight_to'] = (float)$this->getRowValue($rowData, $headingArray, "weight_to");
                $temp['shipping_method_id'] = $shippingMethodId;
                $temp['partner_id'] = $this->getRowValue($rowData, $headingArray, "partner_id");
                $temp['free_shipping'] = $this->getRowValue($rowData, $headingArray, "free_shipping");
                $temp['cart_total'] = (float)$this->getRowValue($rowData, $headingArray, "cart_total");
                $temp['cost'] = (float)$this->getRowValue($rowData, $headingArray, "cost");

                if($temp['dest_country_id'] == '' ||
                    $temp['dest_region_id'] == '' ||
                    $temp['dest_zip'] == '' ||
                    $temp['dest_zip_to'] == ''
                ){
                    continue;
                }
                $temp['price'] = $temp['price']?$temp['price']:0.0000;
                $temp['weight_from'] = $temp['weight_from']?$temp['weight_from']:0.0000;
                $temp['weight_to'] = $temp['weight_to']?$temp['weight_to']:0.0000;
                $temp['cart_total'] = $temp['cart_total']?$temp['cart_total']:0.0000;
                $temp['cost'] = $temp['cost']?$temp['cost']:0.0000;
                $temp['dest_region_id'] = strtoupper($temp['dest_region_id']);
                $temp['dest_zip'] = strtoupper($temp['dest_zip']);
                $temp['dest_zip_to'] = strtoupper($temp['dest_zip_to']);
                $this->addDataToCollection($temp, $shippingMethodId, $temp['partner_id']);
            }
            if (($count - 1) > 1) {
                $this->messageManager->addNotice(__('Some rows are not valid!'));
            }
            if (($count - 1) <= 1) {
                $this->messageManager
                    ->addSuccess(
                        __('Your shipping detail has been successfully Saved')
                    );
            }
            return $this->resultRedirectFactory->create()->setPath('*/*/index');
        } else {
            $this->messageManager->addError(__('Please upload Csv file'));
        }
    }

    /**
     * Get shipping id by shipping method name
     *
     * @param string $shippingMethodName
     * @return int
     */
    public function getShippingNameById($shippingMethodName)
    {
        $entityId = 0;
        $shippingMethodModel = $this->_mpshippingMethodFactory->create()
            ->getCollection()
            ->addFieldToFilter('method_name', $shippingMethodName);
        foreach ($shippingMethodModel as $shippingMethod) {
            $entityId = $shippingMethod->getEntityId();
        }
        return $entityId;
    }

    /**
     * get row value
     *
     * @param mixed $rowData
     * @param mixed|array $headingArray
     * @param string $column_name
     * @return string
     */
    public function getRowValue($rowData, $headingArray, $column_name)
    {
        $rowIndex = isset($headingArray[$column_name]) ? $headingArray[$column_name] : -1;
        return isset($rowData[$rowIndex]) ?$rowData[$rowIndex] : "";
    }

    /**
     * add data to collection
     *
     * @param mixed $temp
     * @param string|int $shippingMethodId
     * @param string|int $partnerid
     * @return void
     */
    public function addDataToCollection($temp, $shippingMethodId, $partnerid)
    {
        $collection = $this->_mpshipping->create()
            ->getCollection()
            ->addFieldToFilter('weight_to', $temp["weight_to"])
            ->addFieldToFilter('dest_zip_to', $temp["dest_zip_to"])
            ->addFieldToFilter('dest_country_id', $temp["dest_country_id"])
            ->addFieldToFilter('partner_id', $partnerid)
            ->addFieldToFilter('dest_region_id', $temp["dest_region_id"])
            ->addFieldToFilter('dest_zip', $temp["dest_zip"])
            ->addFieldToFilter('weight_from', $temp["weight_from"])
            ->addFieldToFilter('cart_total', $temp["cart_total"])
            ->addFieldToFilter('shipping_method_id', $shippingMethodId);

        if ($collection->getSize() > 0) {
            foreach ($collection as $row) {
                $dataArray = [
                    'price' => $temp["price"],
                    'free_shipping' => $temp['free_shipping']?(float)$temp['free_shipping']:0.0000
                ];
                $shippingModel = $this->_mpshipping->create();
                $this->_mpshippingResource->load($shippingModel, $row->getLofshippingId());
                $shippingModel->addData($dataArray);
                $this->_mpshippingResource->save($shippingModel);
            }
        } else {
            $shippingModel = $this->_mpshipping->create();
            $shippingModel->setData($temp);
            $this->_mpshippingResource->save($shippingModel);
        }
    }

    /**
     * Calculate shipping method id
     *
     * @param string  $shippingMethodName
     * @return int
     */
    public function calculateShippingMethodId($shippingMethodName)
    {
        $shippingMethodId = $this->getShippingNameById($shippingMethodName);
        if ($shippingMethodId==0) {
            $savedMethod = $this->_mpshippingMethodFactory->create();
            $savedMethod->setMethodName($shippingMethodName);
            $this->_resource->save($savedMethod);
            $shippingMethodId = $savedMethod->getEntityId();
        }
        return $shippingMethodId;
    }
}
