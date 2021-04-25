<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Merchant\Business\Model;

use Generated\Shared\Transfer\MerchantCollectionTransfer;
use Generated\Shared\Transfer\MerchantCriteriaTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Spryker\Zed\Merchant\Business\Exception\MerchantNotFoundException;
use Spryker\Zed\Merchant\Persistence\MerchantRepositoryInterface;

class MerchantReader implements MerchantReaderInterface
{
    /**
     * @var \Spryker\Zed\Merchant\Persistence\MerchantRepositoryInterface
     */
    protected $repository;

    /**
     * @param \Spryker\Zed\Merchant\Persistence\MerchantRepositoryInterface $repository
     */
    public function __construct(MerchantRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantCriteriaTransfer $merchantCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantCollectionTransfer
     */
    public function get(MerchantCriteriaTransfer $merchantCriteriaTransfer): MerchantCollectionTransfer
    {
        $merchantCollectionTransfer = $this->repository->get($merchantCriteriaTransfer);
        $merchantCollectionTransfer = $this->expandMerchantCollection($merchantCollectionTransfer);

        return $merchantCollectionTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     *
     * @throws \Spryker\Zed\Merchant\Business\Exception\MerchantNotFoundException
     *
     * @return \Generated\Shared\Transfer\MerchantTransfer
     */
    public function getMerchantById(MerchantTransfer $merchantTransfer): MerchantTransfer
    {
        $merchantTransfer->requireIdMerchant();

        $merchantTransfer = $this->repository->getMerchantById($merchantTransfer->getIdMerchant());
        if (!$merchantTransfer) {
            throw new MerchantNotFoundException();
        }

        return $merchantTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantTransfer|null
     */
    public function findMerchantById(MerchantTransfer $merchantTransfer): ?MerchantTransfer
    {
        $merchantTransfer->requireIdMerchant();

        return $this->repository->getMerchantById($merchantTransfer->getIdMerchant());
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantCollectionTransfer $merchantCollectionTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantCollectionTransfer
     */
    protected function expandMerchantCollection(MerchantCollectionTransfer $merchantCollectionTransfer): MerchantCollectionTransfer
    {
        $merchantIds = $this->getMerchantIds($merchantCollectionTransfer);
        $merchantStoreRelationTransferMap = $this->repository->getMerchantStoreRelationMapByMerchantIds($merchantIds);
        $merchantUrlTransfersMap = $this->repository->getUrlsMapByMerchantIds($merchantIds);

        $merchantTransfers = new \ArrayObject();
        foreach ($merchantCollectionTransfer->getMerchants() as $merchantTransfer) {
            $merchantTransfer->setStoreRelation($merchantStoreRelationTransferMap[$merchantTransfer->getIdMerchant()]);
            $merchantTransfer->setUrlCollection(new \ArrayObject($merchantUrlTransfersMap[$merchantTransfer->getIdMerchant()] ?? []));

            $merchantTransfers->append($merchantTransfer);
        }
        $merchantCollectionTransfer->setMerchants($merchantTransfers);

        return $merchantCollectionTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantCollectionTransfer $merchantCollectionTransfer
     *
     * @return int[]
     */
    protected function getMerchantIds(MerchantCollectionTransfer $merchantCollectionTransfer): array
    {
        return array_map(function (MerchantTransfer $merchantTransfer): int {
            /** @var int $idMerchant */
            $idMerchant = $merchantTransfer->getIdMerchant();

            return $idMerchant;
        }, $merchantCollectionTransfer->getMerchants()->getArrayCopy());
    }
}
