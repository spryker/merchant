<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Merchant\Persistence;

use Generated\Shared\Transfer\FilterTransfer;
use Generated\Shared\Transfer\MerchantCollectionTransfer;
use Generated\Shared\Transfer\MerchantCriteriaTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Propel\Runtime\Formatter\ObjectFormatter;
use Spryker\Zed\Kernel\Persistence\AbstractRepository;
use Orm\Zed\Merchant\Persistence\Map\SpyMerchantTableMap;

/**
 * @method \Spryker\Zed\Merchant\Persistence\MerchantPersistenceFactory getFactory()
 */
class MerchantRepository extends AbstractRepository implements MerchantRepositoryInterface
{
    protected const DEFAULT_ORDER_COLUMN = SpyMerchantTableMap::COL_NAME;

    /**
     * @param \Generated\Shared\Transfer\MerchantCriteriaTransfer $merchantCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantCollectionTransfer
     */
    public function get(MerchantCriteriaTransfer $merchantCriteriaTransfer): MerchantCollectionTransfer
    {
        $merchantQuery = $this->getFactory()->createMerchantQuery();

        $filterTransfer = $merchantCriteriaTransfer->getFilter();
        if ($filterTransfer === null || empty($filterTransfer->getOrderBy())) {
            $filterTransfer = (new FilterTransfer())->setOrderBy(static::DEFAULT_ORDER_COLUMN);
        }

        $merchantQuery = $this->applyFilters($merchantQuery, $merchantCriteriaTransfer);
        $merchantQuery = $this->buildQueryFromCriteria($merchantQuery, $filterTransfer)->setFormatter(ObjectFormatter::class);

        /** @var \Orm\Zed\Merchant\Persistence\SpyMerchant[] $merchantCollection */
        $merchantCollection = $this->getPaginatedCollection($merchantQuery, $merchantCriteriaTransfer->getPagination());

        $merchantCollectionTransfer = $this->getFactory()
            ->createPropelMerchantMapper()
            ->mapMerchantCollectionToMerchantCollectionTransfer($merchantCollection, new MerchantCollectionTransfer());

        $merchantCollectionTransfer->setPagination($merchantCriteriaTransfer->getPagination());

        return $merchantCollectionTransfer;
    }

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

        if ($merchantCriteriaTransfer->getEmail() !== null) {
            $merchantQuery->filterByEmail($merchantCriteriaTransfer->getEmail());
        }

        if ($merchantCriteriaTransfer->getMerchantReference() !== null) {
            $merchantQuery->filterByMerchantReference($merchantCriteriaTransfer->getMerchantReference());
        }

        if ($merchantCriteriaTransfer->getMerchantReferences()) {
            $merchantQuery->filterByMerchantReference_In($merchantCriteriaTransfer->getMerchantReferences());
        }

        if ($merchantCriteriaTransfer->getMerchantIds()) {
            $merchantQuery->filterByIdMerchant_In($merchantCriteriaTransfer->getMerchantIds());
        }

        if ($merchantCriteriaTransfer->getIsActive() !== null) {
            $merchantQuery->filterByIsActive($merchantCriteriaTransfer->getIsActive());
        }

        if ($merchantCriteriaTransfer->getStatus() !== null) {
            $merchantQuery->filterByStatus($merchantCriteriaTransfer->getStatus());
        }

        if ($merchantCriteriaTransfer->getStore() !== null) {
            $merchantQuery->useSpyMerchantStoreQuery()
                ->useSpyStoreQuery()
                ->filterByName($merchantCriteriaTransfer->getStore()->getName())
                ->endUse()
                ->endUse();
        }

        return $merchantQuery;
    }

    /**
     * @param \Propel\Runtime\ActiveQuery\ModelCriteria $query
     * @param \Generated\Shared\Transfer\PaginationTransfer|null $paginationTransfer
     *
     * @return mixed|\Propel\Runtime\ActiveRecord\ActiveRecordInterface[]|\Propel\Runtime\Collection\Collection|\Propel\Runtime\Collection\ObjectCollection
     */
    protected function getPaginatedCollection(ModelCriteria $query, ?PaginationTransfer $paginationTransfer = null)
    {
        if ($paginationTransfer !== null) {
            /** @var int $page */
            $page = $paginationTransfer
                ->requirePage()
                ->getPage();

            /** @var int $maxPerPage */
            $maxPerPage = $paginationTransfer
                ->requireMaxPerPage()
                ->getMaxPerPage();

            $paginationModel = $query->paginate($page, $maxPerPage);

            $paginationTransfer->setNbResults($paginationModel->getNbResults());
            $paginationTransfer->setFirstIndex($paginationModel->getFirstIndex());
            $paginationTransfer->setLastIndex($paginationModel->getLastIndex());
            $paginationTransfer->setFirstPage($paginationModel->getFirstPage());
            $paginationTransfer->setLastPage($paginationModel->getLastPage());
            $paginationTransfer->setNextPage($paginationModel->getNextPage());
            $paginationTransfer->setPreviousPage($paginationModel->getPreviousPage());

            return $paginationModel->getResults();
        }

        return $query->find();
    }
}
