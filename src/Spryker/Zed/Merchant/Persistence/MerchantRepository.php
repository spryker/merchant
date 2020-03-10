<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Merchant\Persistence;

use Generated\Shared\Transfer\FilterTransfer;
use Generated\Shared\Transfer\MerchantCollectionTransfer;
use Generated\Shared\Transfer\MerchantCriteriaFilterTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Generated\Shared\Transfer\PaginationTransfer;
use Orm\Zed\Merchant\Persistence\Map\SpyMerchantTableMap;
use Orm\Zed\Merchant\Persistence\SpyMerchantQuery;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Spryker\Zed\Kernel\Persistence\AbstractRepository;

/**
 * @method \Spryker\Zed\Merchant\Persistence\MerchantPersistenceFactory getFactory()
 */
class MerchantRepository extends AbstractRepository implements MerchantRepositoryInterface
{
    protected const DEFAULT_ORDER_COLUMN = SpyMerchantTableMap::COL_NAME;

    /**
     * @param \Generated\Shared\Transfer\MerchantCriteriaFilterTransfer $merchantCriteriaFilterTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantCollectionTransfer
     */
    public function find(MerchantCriteriaFilterTransfer $merchantCriteriaFilterTransfer): MerchantCollectionTransfer
    {
        $merchantQuery = $this->getFactory()
            ->createMerchantQuery();

        $filterTransfer = $merchantCriteriaFilterTransfer->getFilter();
        if ($filterTransfer === null || empty($filterTransfer->getOrderBy())) {
            $filterTransfer = (new FilterTransfer())->setOrderBy(static::DEFAULT_ORDER_COLUMN);
        }

        $merchantQuery = $this->applyFilters($merchantQuery, $merchantCriteriaFilterTransfer);
        $merchantQuery = $this->buildQueryFromCriteria($merchantQuery, $filterTransfer);

        /** @var \Generated\Shared\Transfer\SpyMerchantEntityTransfer[] $merchantCollection */
        $merchantCollection = $this->getPaginatedCollection($merchantQuery, $merchantCriteriaFilterTransfer->getPagination());

        $merchantCollectionTransfer = $this->getFactory()
            ->createPropelMerchantMapper()
            ->mapMerchantCollectionToMerchantCollectionTransfer($merchantCollection, new MerchantCollectionTransfer());

        $merchantCollectionTransfer->setPagination($merchantCriteriaFilterTransfer->getPagination());

        return $merchantCollectionTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantCriteriaFilterTransfer $merchantCriteriaFilterTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantTransfer|null
     */
    public function findOne(MerchantCriteriaFilterTransfer $merchantCriteriaFilterTransfer): ?MerchantTransfer
    {
        $merchantQuery = $this->getFactory()->createMerchantQuery();
        $merchantEntity = $this->applyFilters($merchantQuery, $merchantCriteriaFilterTransfer)->findOne();

        if (!$merchantEntity) {
            return null;
        }

        return $this->getFactory()
            ->createPropelMerchantMapper()
            ->mapMerchantEntityToMerchantTransfer($merchantEntity, new MerchantTransfer());
    }

    /**
     * @param \Orm\Zed\Merchant\Persistence\SpyMerchantQuery $merchantQuery
     * @param \Generated\Shared\Transfer\MerchantCriteriaFilterTransfer $merchantCriteriaFilterTransfer
     *
     * @return \Orm\Zed\Merchant\Persistence\SpyMerchantQuery
     */
    protected function applyFilters(SpyMerchantQuery $merchantQuery, MerchantCriteriaFilterTransfer $merchantCriteriaFilterTransfer): SpyMerchantQuery
    {
        if ($merchantCriteriaFilterTransfer->getIdMerchant() !== null) {
            $merchantQuery->filterByIdMerchant($merchantCriteriaFilterTransfer->getIdMerchant());
        }

        if ($merchantCriteriaFilterTransfer->getEmail() !== null) {
            $merchantQuery->filterByEmail($merchantCriteriaFilterTransfer->getEmail());
        }

        if ($merchantCriteriaFilterTransfer->getMerchantReference() !== null) {
            $merchantQuery->filterByMerchantReference($merchantCriteriaFilterTransfer->getMerchantReference());
        }

        if ($merchantCriteriaFilterTransfer->getIsActive() !== null) {
            $merchantQuery->filterByIsActive($merchantCriteriaFilterTransfer->getIsActive());
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
            $page = $paginationTransfer
                ->requirePage()
                ->getPage();

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
