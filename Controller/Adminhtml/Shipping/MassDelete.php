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

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Lof\TableRateShipping\Model\ResourceModel\Shipping\CollectionFactory;
/**
 * Class MassDelete
 */
class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * Field id
     */
    const ID_FIELD = 'lofshipping_id';

    /**
     * Resource collection
     *
     * @var string
     */
    protected $_collection = 'Lof\TableRateShipping\Model\ResourceModel\Shipping\Collection';

    /**
     * Page model
     *
     * @var string
     */
    protected $_model = 'Lof\TableRateShipping\Model\Shipping';
    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();

        foreach ($collection as $seller) {
            $seller->delete();
        }

        $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $collectionSize));

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Delete selected items
     *
     * @param array $selected
     * @return void
     * @throws \Exception
     */
    protected function selectedDelete(array $selected)
    {
        /** @var AbstractCollection $collection */
        $collection = $this->_objectManager->get($this->_collection);
        $collection->addFieldToFilter(static::ID_FIELD, ['in' => $selected]);
        $this->setSuccessMessage($this->delete($collection));
    }

    /**
     * Delete collection items
     *
     * @param AbstractCollection $collection
     * @return int
     */
    protected function delete(AbstractCollection $collection)
    {
        $count = 0;
        foreach ($collection->getAllIds() as $id) {
            $model = $this->_objectManager->get($this->_model);
            $model->load($id);
            $model->delete();
            ++$count;
        }
        return $count;
    }

    /**
     * Set error messages
     *
     * @param int $count
     * @return void
     */
    protected function setSuccessMessage($count)
    {
        $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $count));
    }

    /**
     * @inheritdoc
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_TableRateShipping::shipping_delete');
    }
}
