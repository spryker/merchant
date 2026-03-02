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
    public function get(MerchantCriteriaTransfer $merchantCriteriaTransfer): MerchantCollectionTransfer;

    public function findOne(MerchantCriteriaTransfer $merchantCriteriaTransfer): ?MerchantTransfer;

    /**
     * @param array<int> $merchantIds
     *
     * @return array<\Generated\Shared\Transfer\StoreRelationTransfer>
     */
    public function getMerchantStoreRelationMapByMerchantIds(array $merchantIds): array;

    /**
     * @param array<int> $merchantIds
     *
     * @return array<array<\Generated\Shared\Transfer\UrlTransfer>>
     */
    public function getUrlsMapByMerchantIds(array $merchantIds): array;
}
