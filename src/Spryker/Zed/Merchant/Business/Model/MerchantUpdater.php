<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Merchant\Business\Model;

use Generated\Shared\Transfer\MerchantCriteriaFilterTransfer;
use Generated\Shared\Transfer\MerchantErrorTransfer;
use Generated\Shared\Transfer\MerchantResponseTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Spryker\Zed\Kernel\Persistence\EntityManager\TransactionTrait;
use Spryker\Zed\Merchant\Business\Model\Status\MerchantStatusValidatorInterface;
use Spryker\Zed\Merchant\Persistence\MerchantEntityManagerInterface;
use Spryker\Zed\Merchant\Persistence\MerchantRepositoryInterface;

class MerchantUpdater implements MerchantUpdaterInterface
{
    use TransactionTrait;

    protected const ERROR_MESSAGE_MERCHANT_NOT_FOUND = 'Merchant is not found.';
    protected const ERROR_MESSAGE_MERCHANT_STATUS_TRANSITION_NOT_VALID = 'Merchant status transition is not valid.';

    /**
     * @var \Spryker\Zed\Merchant\Persistence\MerchantEntityManagerInterface
     */
    protected $merchantEntityManager;

    /**
     * @var \Spryker\Zed\Merchant\Persistence\MerchantRepositoryInterface
     */
    protected $merchantRepository;

    /**
     * @var \Spryker\Zed\Merchant\Business\Model\Status\MerchantStatusValidatorInterface
     */
    protected $merchantStatusValidator;

    /**
     * @var \Spryker\Zed\MerchantExtension\Dependency\Plugin\MerchantPostSavePluginInterface[]
     */
    protected $merchantPostSavePlugins;

    /**
     * @param \Spryker\Zed\Merchant\Persistence\MerchantEntityManagerInterface $merchantEntityManager
     * @param \Spryker\Zed\Merchant\Persistence\MerchantRepositoryInterface $merchantRepository
     * @param \Spryker\Zed\Merchant\Business\Model\Status\MerchantStatusValidatorInterface $merchantStatusValidator
     * @param \Spryker\Zed\MerchantExtension\Dependency\Plugin\MerchantPostSavePluginInterface[] $merchantPostSavePlugins
     */
    public function __construct(
        MerchantEntityManagerInterface $merchantEntityManager,
        MerchantRepositoryInterface $merchantRepository,
        MerchantStatusValidatorInterface $merchantStatusValidator,
        array $merchantPostSavePlugins
    ) {
        $this->merchantEntityManager = $merchantEntityManager;
        $this->merchantRepository = $merchantRepository;
        $this->merchantStatusValidator = $merchantStatusValidator;
        $this->merchantPostSavePlugins = $merchantPostSavePlugins;
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantResponseTransfer
     */
    public function update(MerchantTransfer $merchantTransfer): MerchantResponseTransfer
    {
        $this->assertDefaultMerchantRequirements($merchantTransfer);
        $merchantTransfer->requireIdMerchant();

        $merchantResponseTransfer = $this->createMerchantResponseTransfer();

        $merchantCriteriaFilterTransfer = new MerchantCriteriaFilterTransfer();
        $merchantCriteriaFilterTransfer->setIdMerchant($merchantTransfer->getIdMerchant());

        $existingMerchantTransfer = $this->merchantRepository->findOne($merchantCriteriaFilterTransfer);
        if ($existingMerchantTransfer === null) {
            $merchantResponseTransfer = $this->addMerchantError($merchantResponseTransfer, static::ERROR_MESSAGE_MERCHANT_NOT_FOUND);

            return $merchantResponseTransfer;
        }

        if (!$this->merchantStatusValidator->isMerchantStatusTransitionValid($existingMerchantTransfer->getStatus(), $merchantTransfer->getStatus())) {
            $merchantResponseTransfer = $this->addMerchantError($merchantResponseTransfer, static::ERROR_MESSAGE_MERCHANT_STATUS_TRANSITION_NOT_VALID);

            return $merchantResponseTransfer;
        }

        $merchantTransfer = $this->getTransactionHandler()->handleTransaction(function () use ($merchantTransfer) {
            return $this->executeUpdateTransaction($merchantTransfer);
        });

        $merchantResponseTransfer = $merchantResponseTransfer
            ->setIsSuccess(true)
            ->setMerchant($merchantTransfer);

        return $merchantResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantTransfer
     */
    protected function executeUpdateTransaction(MerchantTransfer $merchantTransfer): MerchantTransfer
    {
        $merchantTransfer = $this->merchantEntityManager->saveMerchant($merchantTransfer);
        $merchantTransfer = $this->executeMerchantPostSavePlugins($merchantTransfer);

        return $merchantTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantTransfer
     */
    protected function executeMerchantPostSavePlugins(MerchantTransfer $merchantTransfer): MerchantTransfer
    {
        foreach ($this->merchantPostSavePlugins as $merchantPostSavePlugin) {
            $merchantTransfer = $merchantPostSavePlugin->execute($merchantTransfer);
        }

        return $merchantTransfer;
    }

    /**
     * @return \Generated\Shared\Transfer\MerchantResponseTransfer
     */
    protected function createMerchantResponseTransfer(): MerchantResponseTransfer
    {
        return (new MerchantResponseTransfer())
            ->setIsSuccess(false);
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     *
     * @return void
     */
    protected function assertDefaultMerchantRequirements(MerchantTransfer $merchantTransfer): void
    {
        $merchantTransfer
            ->requireName()
            ->requireEmail();
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantResponseTransfer $merchantResponseTransfer
     * @param string $message
     *
     * @return \Generated\Shared\Transfer\MerchantResponseTransfer
     */
    protected function addMerchantError(MerchantResponseTransfer $merchantResponseTransfer, string $message): MerchantResponseTransfer
    {
        $merchantResponseTransfer->addError((new MerchantErrorTransfer())->setMessage($message));

        return $merchantResponseTransfer;
    }
}