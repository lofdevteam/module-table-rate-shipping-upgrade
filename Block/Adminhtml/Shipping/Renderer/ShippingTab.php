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
namespace Lof\TableRateShipping\Block\Adminhtml\Shipping\Renderer;
use Lof\TableRateShipping\Model\ShippingmethodFactory;
class ShippingTab  extends \Magento\Backend\Block\Widget\Form\Renderer\Fieldset\Element

{

    /**
     * @var string
     */
    protected $_template = 'shipping/shippingtab.phtml';

    /**
     * Prepare global layout
     * Add "Add tier" button to layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
}
