<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Merchant\Persistence;

use Generated\Shared\Transfer\MerchantCollectionTransfer;
use Generated\Shared\Transfer\MerchantCriteriaTransfer;
use Generated\Shared\Transfer\MerchantTransfer;

interface MerchantRepositoryInterface
{
    /**
     * @param \Generated\Shared\Transfer\MerchantCriteriaTransfer $merchantCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantCollectionTransfer
     */
    public function get(MerchantCriteriaTransfer $merchantCriteriaTransfer): MerchantCollectionTransfer;

    /**
     * Specification:
     * - Returns a MerchantTransfer by merchant id.
     * - Returns null in case a record is not found.
     *
     * @param int $idMerchant
     *
     * @return \Generated\Shared\Transfer\MerchantTransfer|null
     */
    public function getMerchantById(int $idMerchant): ?MerchantTransfer;

    /**
     * Specification:
     * - Retrieves collection of all merchants.
     * - List of merchants is ordered by merchant name.
     *
     * @return \Generated\Shared\Transfer\MerchantCollectionTransfer
     */
    public function getMerchants(): MerchantCollectionTransfer;

    /**
     * Specification:
     * - Checks whether merchant key already exists.
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasKey(string $key): bool;

    /**
     * @param int[] $merchantIds
     *
     * @return \Generated\Shared\Transfer\UrlTransfer[][]
     */
    public function getGroupedUrlsByMerchantIds(array $merchantIds): array;
}
