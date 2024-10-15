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

namespace Lof\TableRateShipping\Api;

interface TableRateShippingRepositoryInterface
{

    /**
     * Save TableRateShipping
     * @param \Lof\TableRateShipping\Api\Data\TableRateShippingInterface $TableRateShipping
     * @return \Lof\TableRateShipping\Api\Data\TableRateShippingInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Lof\TableRateShipping\Api\Data\TableRateShippingInterface $TableRateShipping
    );

    /**
     * Retrieve TableRateShipping
     * @param string $tableRateShippingId
     * @return \Lof\TableRateShipping\Api\Data\TableRateShippingInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($tableRateShippingId);

    /**
     * Retrieve TableRateShipping matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lof\TableRateShipping\Api\Data\TableRateShippingSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete TableRateShipping
     * @param \Lof\TableRateShipping\Api\Data\TableRateShippingInterface $TableRateShipping
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Lof\TableRateShipping\Api\Data\TableRateShippingInterface $TableRateShipping
    );

    /**
     * Delete TableRateShipping by ID
     * @param string $tableRateShippingId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($tableRateShippingId);
}
