<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Merchant\Business;

use Generated\Shared\Transfer\MerchantCriteriaTransfer;
use Generated\Shared\Transfer\MerchantCollectionTransfer;
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
     * {@inheritdoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantTransfer
     */
    public function createMerchant(MerchantTransfer $merchantTransfer): MerchantTransfer
    {
        return $this->getFactory()
            ->createMerchantWriter()
            ->create($merchantTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantTransfer
     */
    public function updateMerchant(MerchantTransfer $merchantTransfer): MerchantTransfer
    {
        return $this->getFactory()
            ->createMerchantWriter()
            ->update($merchantTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     *
     * @return void
     */
    public function deleteMerchant(MerchantTransfer $merchantTransfer): void
    {
        $this->getFactory()
            ->createMerchantWriter()
            ->delete($merchantTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantTransfer
     */
    public function getMerchantById(MerchantTransfer $merchantTransfer): MerchantTransfer
    {
        return $this->getFactory()
            ->createMerchantReader()
            ->getMerchantById($merchantTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @return \Generated\Shared\Transfer\MerchantCollectionTransfer
     */
    public function getMerchants(): MerchantCollectionTransfer
    {
        return $this->getRepository()->getMerchants();
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantTransfer|null
     */
    public function findMerchantById(MerchantTransfer $merchantTransfer): ?MerchantTransfer
    {
        return $this->getFactory()
            ->createMerchantReader()
            ->findMerchantById($merchantTransfer);
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
}
