<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Merchant\Business;

use Generated\Shared\Transfer\MerchantCollectionTransfer;
use Generated\Shared\Transfer\MerchantCriteriaTransfer;
use Generated\Shared\Transfer\MerchantExportCriteriaTransfer;
use Generated\Shared\Transfer\MerchantPublisherConfigTransfer;
use Generated\Shared\Transfer\MerchantResponseTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * @method \Spryker\Zed\Merchant\Business\MerchantBusinessFactory getFactory()
 * @method \Spryker\Zed\Merchant\Persistence\MerchantRepositoryInterface getRepository()
 * @method \Spryker\Zed\Merchant\Persistence\MerchantEntityManagerInterface getEntityManager()
 */
class MerchantFacade extends AbstractFacade implements MerchantFacadeInterface
{
    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantResponseTransfer
     */
    public function createMerchant(MerchantTransfer $merchantTransfer): MerchantResponseTransfer
    {
        return $this->getFactory()
            ->createMerchantCreator()
            ->create($merchantTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantResponseTransfer
     */
    public function updateMerchant(MerchantTransfer $merchantTransfer): MerchantResponseTransfer
    {
        return $this->getFactory()
            ->createMerchantUpdater()
            ->update($merchantTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MerchantCriteriaTransfer $merchantCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantCollectionTransfer
     */
    public function get(MerchantCriteriaTransfer $merchantCriteriaTransfer): MerchantCollectionTransfer
    {
        return $this->getFactory()
            ->createMerchantReader()
            ->get($merchantCriteriaTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MerchantCriteriaTransfer $merchantCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantTransfer|null
     */
    public function findOne(MerchantCriteriaTransfer $merchantCriteriaTransfer): ?MerchantTransfer
    {
        return $this->getFactory()
            ->createMerchantReader()
            ->findOne($merchantCriteriaTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param string $currentStatus
     *
     * @return array<string>
     */
    public function getApplicableMerchantStatuses(string $currentStatus): array
    {
        return $this->getFactory()->createMerchantStatusReader()->getApplicableMerchantStatuses($currentStatus);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param array<\Generated\Shared\Transfer\PriceProductMerchantRelationshipStorageTransfer> $priceProductMerchantRelationshipStorageTransfers
     *
     * @return array<\Generated\Shared\Transfer\PriceProductMerchantRelationshipStorageTransfer>
     */
    public function filterPriceProductMerchantRelations(array $priceProductMerchantRelationshipStorageTransfers): array
    {
        return $this->getFactory()
            ->createPriceProductMerchantRelationshipStorageFilter()
            ->filterByMerchant($priceProductMerchantRelationshipStorageTransfers);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @deprecated Will be removed without replacement.
     *
     * @param \Generated\Shared\Transfer\MerchantExportCriteriaTransfer $merchantExportCriteriaTransfer
     *
     * @return void
     */
    public function triggerMerchantExportEvents(MerchantExportCriteriaTransfer $merchantExportCriteriaTransfer): void
    {
        $this->getFactory()
            ->createMerchantExporter()
            ->export($merchantExportCriteriaTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @deprecated Will be removed without replacement.
     *
     * @param \Generated\Shared\Transfer\MerchantPublisherConfigTransfer $merchantPublisherConfigTransfer
     *
     * @return void
     */
    public function emitPublishMerchantToMessageBroker(MerchantPublisherConfigTransfer $merchantPublisherConfigTransfer): void
    {
        $this->getFactory()->createMerchantMessageBrokerPublisher()->publish($merchantPublisherConfigTransfer);
    }
}
