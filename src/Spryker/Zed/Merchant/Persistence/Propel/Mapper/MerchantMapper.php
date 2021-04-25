<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Merchant\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\MerchantCollectionTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Generated\Shared\Transfer\StoreRelationTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use Generated\Shared\Transfer\UrlTransfer;
use Orm\Zed\Merchant\Persistence\SpyMerchant;
use Orm\Zed\Store\Persistence\SpyStore;
use Orm\Zed\Url\Persistence\SpyUrl;

class MerchantMapper implements MerchantMapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     * @param \Orm\Zed\Merchant\Persistence\SpyMerchant $spyMerchant
     *
     * @return \Orm\Zed\Merchant\Persistence\SpyMerchant
     */
    public function mapMerchantTransferToEntity(
        MerchantTransfer $merchantTransfer,
        SpyMerchant $spyMerchant
    ): SpyMerchant {
        $spyMerchant->fromArray(
            $merchantTransfer->modifiedToArray(false)
        );

        return $spyMerchant;
    }

    /**
     * @param \Orm\Zed\Merchant\Persistence\SpyMerchant $spyMerchant
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantTransfer
     */
    public function mapEntityToMerchantTransfer(
        SpyMerchant $spyMerchant,
        MerchantTransfer $merchantTransfer
    ): MerchantTransfer {
        return $merchantTransfer->fromArray(
            $spyMerchant->toArray(),
            true
        );
    }


    /**
     * @param \Orm\Zed\Merchant\Persistence\SpyMerchant[] $merchantEntities
     * @param \Generated\Shared\Transfer\MerchantCollectionTransfer $merchantCollectionTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantCollectionTransfer
     */
    public function mapMerchantCollectionToMerchantCollectionTransfer(
        $merchantEntities,
        MerchantCollectionTransfer $merchantCollectionTransfer
    ): MerchantCollectionTransfer {
        $merchants = new \ArrayObject();

        foreach ($merchantEntities as $merchantEntity) {
            $merchants->append($this->mapMerchantEntityToMerchantTransfer($merchantEntity, new MerchantTransfer()));
        }

        $merchantCollectionTransfer->setMerchants($merchants);

        return $merchantCollectionTransfer;
    }

    /**
     * @param \Orm\Zed\Merchant\Persistence\SpyMerchant $merchantEntity
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantTransfer
     */
    public function mapMerchantEntityToMerchantTransfer(
        SpyMerchant $merchantEntity,
        MerchantTransfer $merchantTransfer
    ): MerchantTransfer {
        return $merchantTransfer->fromArray(
            $merchantEntity->toArray(),
            true
        );
    }

    /**
     * @param \Orm\Zed\Url\Persistence\SpyUrl $urlEntity
     * @param \Generated\Shared\Transfer\UrlTransfer $urlTransfer
     *
     * @return \Generated\Shared\Transfer\UrlTransfer
     */
    public function mapUrlEntityToUrlTransfer(SpyUrl $urlEntity, UrlTransfer $urlTransfer): UrlTransfer
    {
        return $urlTransfer->fromArray($urlEntity->toArray(), true)
            ->setLocaleName($urlEntity->getSpyLocale()->getLocaleName());
    }
}
