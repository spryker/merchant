<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Merchant\Business\Publisher;

use Exception;
use Generated\Shared\Transfer\MerchantCollectionTransfer;
use Generated\Shared\Transfer\MerchantCriteriaTransfer;
use Generated\Shared\Transfer\MerchantPublisherConfigTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Generated\Shared\Transfer\StoreRelationTransfer;
use Spryker\Shared\Kernel\Transfer\Exception\NullValueException;
use Spryker\Shared\Kernel\Transfer\TransferInterface;
use Spryker\Shared\Log\LoggerTrait;
use Spryker\Zed\Merchant\Business\Exception\MerchantPublisherEventNameMismatchException;
use Spryker\Zed\Merchant\Business\Mapper\TransferMapperInterface;
use Spryker\Zed\Merchant\Business\Reader\MerchantReaderInterface;
use Spryker\Zed\Merchant\Dependency\Facade\MerchantToMessageBrokerFacadeInterface;
use Spryker\Zed\Merchant\MerchantConfig;

/**
 * @deprecated Will be removed without replacement.
 */
class MerchantMessageBrokerPublisher implements MerchantPublisherInterface
{
    use LoggerTrait;

    /**
     * @var \Spryker\Zed\Merchant\Dependency\Facade\MerchantToMessageBrokerFacadeInterface
     */
    protected MerchantToMessageBrokerFacadeInterface $messageBrokerFacade;

    /**
     * @var \Spryker\Zed\Merchant\Business\Reader\MerchantReaderInterface
     */
    protected MerchantReaderInterface $merchantReader;

    /**
     * @var \Spryker\Zed\Merchant\Business\Mapper\TransferMapperInterface
     */
    protected TransferMapperInterface $merchantMapper;

    /**
     * @var \Spryker\Zed\Merchant\MerchantConfig
     */
    protected MerchantConfig $merchantConfig;

    public function __construct(
        MerchantToMessageBrokerFacadeInterface $messageBrokerFacade,
        MerchantReaderInterface $merchantReader,
        TransferMapperInterface $merchantMapper,
        MerchantConfig $merchantConfig
    ) {
        $this->messageBrokerFacade = $messageBrokerFacade;
        $this->merchantReader = $merchantReader;
        $this->merchantMapper = $merchantMapper;
        $this->merchantConfig = $merchantConfig;
    }

    public function publish(MerchantPublisherConfigTransfer $merchantPublisherConfigTransfer): void
    {
        $this->assertEventNameIsAllowedForPublish($merchantPublisherConfigTransfer);

        $this->performMerchantPublish($merchantPublisherConfigTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantPublisherConfigTransfer $merchantPublisherConfigTransfer
     *
     * @throws \Spryker\Zed\Merchant\Business\Exception\MerchantPublisherEventNameMismatchException
     *
     * @return void
     */
    protected function assertEventNameIsAllowedForPublish(MerchantPublisherConfigTransfer $merchantPublisherConfigTransfer): void
    {
        if (
            !in_array(
                $merchantPublisherConfigTransfer->getEventName(),
                $this->merchantConfig->getMerchantEventsAllowedForPublish(),
                true,
            )
        ) {
            throw new MerchantPublisherEventNameMismatchException(sprintf('Merchant event `%s` is not allowed for publish.', $merchantPublisherConfigTransfer->getEventName()));
        }
    }

    protected function performMerchantPublish(MerchantPublisherConfigTransfer $merchantPublisherConfigTransfer): void
    {
        $merchantCriteriaTransfer = (new MerchantCriteriaTransfer())->setMerchantIds($merchantPublisherConfigTransfer->getMerchantIds());
        $merchantCollectionTransfer = $this->merchantReader->get($merchantCriteriaTransfer);

        if (!$merchantCollectionTransfer->getMerchants()->count()) {
            $this->getLogger()->warning('No merchants found when trying to publish merchant event.');

            return;
        }

        $this->publishMerchantMessages($merchantCollectionTransfer, $merchantPublisherConfigTransfer->getEventNameOrFail());
    }

    protected function publishMerchantMessages(MerchantCollectionTransfer $merchantCollectionTransfer, string $publishTransferClass): void
    {
        foreach ($merchantCollectionTransfer->getMerchants() as $merchantTransfer) {
            try {
                $publishTransfer = $this->createPublishTransfer(
                    $merchantTransfer,
                    $publishTransferClass,
                );
            } catch (NullValueException $exception) {
                $this->getLogger()->error(sprintf('Failed to createPublishTransfer with message %s', $exception->getMessage()), ['exception' => $exception]);

                continue;
            }

            try {
                $this->messageBrokerFacade->sendMessage($publishTransfer);
            } catch (Exception $exception) {
                $this->getLogger()->error(sprintf('Failed to sendMessage with message %s', $exception->getMessage()), ['exception' => $exception]);
            }
        }
    }

    protected function createPublishTransfer(MerchantTransfer $merchantTransfer, string $publishTransferClass): TransferInterface
    {
        /** @var \Generated\Shared\Transfer\MerchantExportedTransfer|\Generated\Shared\Transfer\MerchantCreatedTransfer|\Generated\Shared\Transfer\MerchantUpdatedTransfer $publishTransfer */
        $publishTransfer = new $publishTransferClass();

        $filteredMerchantData = $this->merchantMapper->mapTransferDataByAllowedFields($merchantTransfer, $this->merchantConfig->getMerchantFieldsForMerchantEventMessage());
        $filteredStoreRelationData = $this->merchantMapper->mapTransferDataByAllowedFields($merchantTransfer->getStoreRelationOrFail(), $this->merchantConfig->getMerchantStoreRelationFieldsForMerchantEventMessage());
        $filteredMerchantTransfer = (new MerchantTransfer())->fromArray($filteredMerchantData, true);
        $filteredStoreRelationTransfer = (new StoreRelationTransfer())->fromArray($filteredStoreRelationData, true);
        $filteredMerchantTransfer->setStoreRelation($filteredStoreRelationTransfer);

        $publishTransfer->setMerchant($filteredMerchantTransfer);

        return $publishTransfer;
    }
}
