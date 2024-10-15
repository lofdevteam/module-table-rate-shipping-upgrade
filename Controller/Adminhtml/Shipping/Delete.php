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

class Delete extends \Lof\TableRateShipping\Controller\Adminhtml\Shipping
{
    /**
     * @inheritdoc
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_TableRateShipping::shipping_delete');
    }

    /**
     * Delete action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('lofshipping_id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create('Lof\TableRateShipping\Model\Shipping');
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccess(__('The shipping has been deleted.'));

                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());

                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['lofshipping_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addError(__('We can\'t find a shipping to delete.'));

        // go to grid
        return $resultRedirect->setPath('*/*/');
    }

}
