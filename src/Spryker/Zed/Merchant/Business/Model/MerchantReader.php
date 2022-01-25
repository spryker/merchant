<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Merchant\Business\Model;

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
     * @var array<\Spryker\Zed\MerchantExtension\Dependency\Plugin\MerchantExpanderPluginInterface>
     */
    protected $merchantExpanderPlugins;

    /**
     * @param \Spryker\Zed\Merchant\Persistence\MerchantRepositoryInterface $repository
     * @param array<\Spryker\Zed\MerchantExtension\Dependency\Plugin\MerchantExpanderPluginInterface> $merchantExpanderPlugins
     */
    public function __construct(MerchantRepositoryInterface $repository, array $merchantExpanderPlugins)
    {
        $this->repository = $repository;
        $this->merchantExpanderPlugins = $merchantExpanderPlugins;
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
     * @param \Generated\Shared\Transfer\MerchantCriteriaTransfer $merchantCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantTransfer|null
     */
    public function findOne(MerchantCriteriaTransfer $merchantCriteriaTransfer): ?MerchantTransfer
    {
        $merchantTransfer = $this->merchantRepository->findOne($merchantCriteriaTransfer);
        if ($merchantTransfer === null) {
            return null;
        }

        return $this->executeMerchantExpanderPlugins($merchantTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantTransfer
     */
    protected function executeMerchantExpanderPlugins(MerchantTransfer $merchantTransfer): MerchantTransfer
    {
        foreach ($this->merchantExpanderPlugins as $merchantExpanderPlugin) {
            $merchantTransfer = $merchantExpanderPlugin->expand($merchantTransfer);
        }

        return $merchantTransfer;
    }
}
