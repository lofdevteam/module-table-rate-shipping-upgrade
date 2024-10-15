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

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

class Exportcsv extends \Lof\TableRateShipping\Controller\Adminhtml\Shipping
{
    protected $_layout;

    /**
     * @var \Lof\TableRateShipping\Model\ShippingFactory
     */
    protected $shippingFactory;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    protected $directory;

    /**
     * @var \Lof\TableRateShipping\Model\ShippingmethodFactory
     */
    protected $shippingmethodFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param Filesystem $filesystem
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Lof\TableRateShipping\Model\ShippingFactory $shippingFactory
     * @param \Lof\TableRateShipping\Model\ShippingmethodFactory $shippingmethodFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\LayoutInterface $layout,
        Filesystem $filesystem,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Lof\TableRateShipping\Model\ShippingFactory $shippingFactory,
        \Lof\TableRateShipping\Model\ShippingmethodFactory $shippingmethodFactory
    ) {
        parent::__construct($context, $coreRegistry);
        $this->_layout = $layout;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->fileFactory = $fileFactory;
        $this->shippingFactory = $shippingFactory;
        $this->shippingmethodFactory = $shippingmethodFactory;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
         /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            // init model and delete
            $collection = $this->shippingFactory->create()->getCollection();
            $params = [];
            foreach ($collection as $key => $model) {
                $params[] = $model->getData();
            }
            $name = 'adminShippingInfo';
            $file = 'export/tablerateshipping/'. $name . '.csv';

            $this->directory->create('export');
            $stream = $this->directory->openFile($file, 'w+');
            $stream->lock();
            $headers = $fields = [];
            $headers = array('country_code','region_id','zip','zip_to','price','weight_from','weight_to','shipping_method','partner_id', 'free_shipping','cart_total','cost');
            $stream->writeCsv($headers);
            foreach ($params as $row) {
                $shipping_method_name = $this->getShippingNameById($row["shipping_method_id"]);
                $rowData = $fields;
                $rowData['country_code'] = $row['dest_country_id'];
                $rowData['region_id'] = $row['dest_region_id'];
                $rowData['zip'] = strip_tags($row['dest_zip']);
                $rowData['zip_to'] = strip_tags($row['dest_zip_to']);
                $rowData['price'] = $row['price'];
                $rowData['weight_from'] = $row['weight_from'];
                $rowData['weight_to'] = $row['weight_to'];
                $rowData['shipping_method'] = strip_tags($shipping_method_name);
                $rowData['partner_id'] = $row['partner_id'];
                $rowData['free_shipping'] = $row['free_shipping'];
                $rowData['cart_total'] = $row['cart_total'];
                $rowData['cost'] = $row['cost'];
                $stream->writeCsv($rowData);
            }
            $stream->unlock();
            $stream->close();
            $file = [
                'type' => 'filename',
                'value' => $file,
                'rm' => true  // can delete file after use
            ];
             // display success message
            $this->messageManager->addSuccess(__('You export table rate shipping to csv success.'));
            return $this->fileFactory->create($name . '.csv', $file, 'var');

        } catch (\Exception $e) {
            // display error message
            $this->messageManager->addError($e->getMessage());
            // go back to edit form
            return $resultRedirect->setPath('*/*/index');
        }
        // display error message
        $this->messageManager->addError(__('We can\'t find a table rate shipping to exportcsv.'));/** @phpstan-ignore-line */
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Get shipping name by id
     *
     * @param int $shippingMethodId
     * @return string
     */
    public function getShippingNameById($shippingMethodId)
    {
        $shippingMethodModel = $this->shippingmethodFactory->create()->load($shippingMethodId);/** @phpstan-ignore-line */
        return $shippingMethodModel->getId()?$shippingMethodModel->getMethodName() : $shippingMethodId;
    }

    /**
     * @inheritdoc
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_TableRateShipping::shipping');
    }
}
