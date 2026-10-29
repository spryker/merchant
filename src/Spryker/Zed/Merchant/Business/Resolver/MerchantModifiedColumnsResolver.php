<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Merchant\Business\Resolver;

use Generated\Shared\Transfer\MerchantTransfer;
use Spryker\Zed\Merchant\MerchantConfig;

class MerchantModifiedColumnsResolver implements MerchantModifiedColumnsResolverInterface
{
    /**
     * @uses \Orm\Zed\Merchant\Persistence\Map\SpyMerchantTableMap::TABLE_NAME
     */
    protected const string TABLE_NAME_MERCHANT = 'spy_merchant';

    /**
     * @uses \Orm\Zed\Merchant\Persistence\Map\SpyMerchantStoreTableMap::COL_FK_STORE
     */
    protected const string COL_MERCHANT_STORE_FK_STORE = 'spy_merchant_store.fk_store';

    public function resolveModifiedColumns(MerchantTransfer $existingMerchantTransfer, MerchantTransfer $merchantTransfer): array
    {
        $modifiedColumns = $this->getModifiedMerchantColumns($existingMerchantTransfer, $merchantTransfer);

        if ($this->isStoreRelationModified($existingMerchantTransfer, $merchantTransfer)) {
            $modifiedColumns[] = static::COL_MERCHANT_STORE_FK_STORE;
        }

        if (!$modifiedColumns) {
            return [MerchantConfig::MODIFIED_COLUMN_MERCHANT_RELATED_DATA];
        }

        return $modifiedColumns;
    }

    /**
     * @return list<string>
     */
    protected function getModifiedMerchantColumns(MerchantTransfer $existingMerchantTransfer, MerchantTransfer $merchantTransfer): array
    {
        // The stored merchant is mapped from the `spy_merchant` entity, so its scalar properties are exactly the merchant
        // columns, also the ones other modules or projects add, and its underscored keys are the column names.
        $storedColumnValues = $existingMerchantTransfer->modifiedToArray(false);
        $modifiedColumns = [];

        foreach ($merchantTransfer->modifiedToArray(false) as $columnName => $value) {
            if (!$this->isColumnValueModified($storedColumnValues, $columnName, $value)) {
                continue;
            }

            $modifiedColumns[] = sprintf('%s.%s', static::TABLE_NAME_MERCHANT, $columnName);
        }

        return $modifiedColumns;
    }

    /**
     * @param array<string, mixed> $storedColumnValues
     */
    protected function isColumnValueModified(array $storedColumnValues, string $columnName, mixed $value): bool
    {
        if (!array_key_exists($columnName, $storedColumnValues) || !$this->isColumnValue($storedColumnValues[$columnName])) {
            return false;
        }

        return $this->isColumnValue($value) && $storedColumnValues[$columnName] !== $value;
    }

    protected function isColumnValue(mixed $value): bool
    {
        return $value === null || is_scalar($value);
    }

    protected function isStoreRelationModified(MerchantTransfer $existingMerchantTransfer, MerchantTransfer $merchantTransfer): bool
    {
        $existingStoreIds = $existingMerchantTransfer->getStoreRelationOrFail()->getIdStores();
        $storeIds = $merchantTransfer->getStoreRelationOrFail()->getIdStores();

        return array_diff($existingStoreIds, $storeIds) !== [] || array_diff($storeIds, $existingStoreIds) !== [];
    }
}
