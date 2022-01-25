<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Merchant\Persistence;

use Generated\Shared\Transfer\MerchantCollectionTransfer;
use Generated\Shared\Transfer\MerchantCriteriaTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Generated\Shared\Transfer\StoreRelationTransfer;
use Generated\Shared\Transfer\UrlTransfer;
use Orm\Zed\Merchant\Persistence\SpyMerchantQuery;
use Propel\Runtime\Collection\ObjectCollection;
use Spryker\Zed\Kernel\Persistence\AbstractRepository;

/**
 * @method \Spryker\Zed\Merchant\Persistence\MerchantPersistenceFactory getFactory()
 */
class MerchantRepository extends AbstractRepository implements MerchantRepositoryInterface
{
    /**
     * {@inheritdoc}
     *
     * @param int $idMerchant
     *
     * @return \Generated\Shared\Transfer\MerchantTransfer|null
     */
    public function getMerchantById(int $idMerchant): ?MerchantTransfer
    {
        $spyMerchant = $this->getFactory()
            ->createMerchantQuery()
            ->filterByIdMerchant($idMerchant)
            ->findOne();

        if (!$spyMerchant) {
            return null;
        }

        return $this->getFactory()
            ->createPropelMerchantMapper()
            ->mapEntityToMerchantTransfer($spyMerchant, new MerchantTransfer());
    }

    /**
     * {@inheritdoc}
     *
     * @return \Generated\Shared\Transfer\MerchantCollectionTransfer
     */
    public function getMerchants(): MerchantCollectionTransfer
    {
        $spyMerchants = $this->getFactory()
            ->createMerchantQuery()
            ->orderByName()
            ->find();

        $mapper = $this->getFactory()
            ->createPropelMerchantMapper();

        $merchantCollectionTransfer = new MerchantCollectionTransfer();
        foreach ($spyMerchants as $spyMerchant) {
            $merchantCollectionTransfer->addMerchants(
                $mapper->mapEntityToMerchantTransfer($spyMerchant, new MerchantTransfer())
            );
        }

        return $merchantCollectionTransfer;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasKey(string $key): bool
    {
        return $this->getFactory()
            ->createMerchantQuery()
            ->filterByMerchantKey($key)
            ->exists();
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantCriteriaTransfer $merchantCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantTransfer|null
     */
    public function findOne(MerchantCriteriaTransfer $merchantCriteriaTransfer): ?MerchantTransfer
    {
        $merchantQuery = $this->getFactory()->createMerchantQuery();
        $merchantEntity = $this->applyFilters($merchantQuery, $merchantCriteriaTransfer)->findOne();

        if (!$merchantEntity) {
            return null;
        }

        return $this->getFactory()
            ->createPropelMerchantMapper()
            ->mapEntityToMerchantTransfer($merchantEntity, new MerchantTransfer());
    }

    /**
     * @param \Orm\Zed\Merchant\Persistence\SpyMerchantQuery $merchantQuery
     * @param \Generated\Shared\Transfer\MerchantCriteriaTransfer $merchantCriteriaTransfer
     *
     * @return \Orm\Zed\Merchant\Persistence\SpyMerchantQuery
     */
    protected function applyFilters(SpyMerchantQuery $merchantQuery, MerchantCriteriaTransfer $merchantCriteriaTransfer): SpyMerchantQuery
    {
        if ($merchantCriteriaTransfer->getIdMerchant() !== null) {
            $merchantQuery->filterByIdMerchant($merchantCriteriaTransfer->getIdMerchant());
        }

        return $merchantQuery;
    }
}
