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

class Edit extends \Lof\TableRateShipping\Controller\Adminhtml\Shipping
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Lof\TableRateShipping\Model\ShippingFactory
     */
    protected $shippingFactory;

    /**
     * @var \Magento\Backend\Model\SessionFactory
     */
    protected $sessionFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Lof\TableRateShipping\Model\ShippingFactory $shippingFactory
     * @param \Magento\Backend\Model\SessionFactory $sessionFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Lof\TableRateShipping\Model\ShippingFactory $shippingFactory,
        \Magento\Backend\Model\SessionFactory $sessionFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->shippingFactory = $shippingFactory;
        $this->sessionFactory = $sessionFactory;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * @inheritdoc
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_TableRateShipping::shipping_save');
    }

    /**
     * Edit TableRateShipping Form
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {

        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('lofshipping_id');
        $model = $this->shippingFactory->create();

        // 2. Initial checking
        if ($id) {
            $model->load($id);/** @phpstan-ignore-line */
            if (!$model->getId()) {
                $this->messageManager->addError(__('This Shipping no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        // 3. Set entered data if was error when we do save
        $data = $this->sessionFactory->create()->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        // 4. Register model to use later in forms
        $this->_coreRegistry->register('loftablerateshipping_shipping', $model);
        $resultPage = $this->resultPageFactory->create();

        // 5. Build edit form
        $this->initPage($resultPage)->addBreadcrumb(
             __('New Shipping'),
             __('New Shipping')
            );
        $resultPage->getConfig()->getTitle()->prepend(__('Table Rate Shipping'));
        if($model->getId()){
            $resultPage->getConfig()->getTitle()->prepend(__('Edit Shipping %1', $model->getId()));
        }else {
            $resultPage->getConfig()->getTitle()->prepend(__('New Shipping'));
        }
        return $resultPage;
    }
}
