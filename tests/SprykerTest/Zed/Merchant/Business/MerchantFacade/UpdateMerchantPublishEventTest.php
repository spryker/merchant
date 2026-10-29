<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Merchant\Business\MerchantFacade;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\MerchantCriteriaTransfer;
use Generated\Shared\Transfer\MerchantResponseTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Generated\Shared\Transfer\StoreRelationTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use Orm\Zed\Merchant\Persistence\Map\SpyMerchantStoreTableMap;
use Orm\Zed\Merchant\Persistence\Map\SpyMerchantTableMap;
use Spryker\Shared\Kernel\Transfer\TransferInterface;
use Spryker\Zed\Merchant\Dependency\Facade\MerchantToEventFacadeInterface;
use Spryker\Zed\Merchant\Dependency\MerchantEvents;
use Spryker\Zed\Merchant\MerchantConfig;
use Spryker\Zed\Merchant\MerchantDependencyProvider;
use SprykerTest\Zed\Merchant\MerchantBusinessTester;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Merchant
 * @group Business
 * @group MerchantFacade
 * @group UpdateMerchantPublishEventTest
 * Add your own group annotations below this line
 */
class UpdateMerchantPublishEventTest extends Unit
{
    protected MerchantBusinessTester $tester;

    /**
     * @var list<\Generated\Shared\Transfer\EventEntityTransfer>
     */
    protected array $publishEventEntityTransfers = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->publishEventEntityTransfers = [];
        $eventFacadeMock = $this->createMock(MerchantToEventFacadeInterface::class);
        $eventFacadeMock->method('trigger')->willReturnCallback(function (string $eventName, TransferInterface $transfer): void {
            if ($eventName === MerchantEvents::MERCHANT_PUBLISH) {
                $this->publishEventEntityTransfers[] = $transfer;
            }
        });

        $this->tester->setDependency(MerchantDependencyProvider::FACADE_EVENT, $eventFacadeMock);
    }

    public function testGivenChangedIsActiveWhenUpdatingMerchantThenPublishEventContainsIsActiveColumn(): void
    {
        // Arrange
        $merchantTransfer = $this->tester->haveMerchant([MerchantTransfer::IS_ACTIVE => false]);
        $merchantTransfer->setIsActive(true);

        // Act
        $this->updateMerchant($merchantTransfer);

        // Assert
        $this->assertContains(SpyMerchantTableMap::COL_IS_ACTIVE, $this->getPublishEventModifiedColumns());
        $this->assertNotContains(MerchantConfig::MODIFIED_COLUMN_MERCHANT_RELATED_DATA, $this->getPublishEventModifiedColumns());
    }

    public function testGivenChangedStatusWhenUpdatingMerchantThenPublishEventContainsStatusColumn(): void
    {
        // Arrange
        $merchantTransfer = $this->tester->haveMerchant([MerchantTransfer::STATUS => MerchantConfig::STATUS_WAITING_FOR_APPROVAL]);
        $merchantTransfer->setStatus(MerchantConfig::STATUS_APPROVED);

        // Act
        $this->updateMerchant($merchantTransfer);

        // Assert
        $this->assertContains(SpyMerchantTableMap::COL_STATUS, $this->getPublishEventModifiedColumns());
    }

    public function testGivenChangedNameOnlyWhenUpdatingMerchantThenPublishEventContainsNameColumnOnly(): void
    {
        // Arrange
        $merchantTransfer = $this->tester->haveMerchant([MerchantTransfer::NAME => 'Old Company Name']);
        $merchantTransfer->setName('New Company Name');

        // Act
        $this->updateMerchant($merchantTransfer);

        // Assert
        $modifiedColumns = $this->getPublishEventModifiedColumns();
        $this->assertContains(SpyMerchantTableMap::COL_NAME, $modifiedColumns);
        $this->assertNotContains(SpyMerchantTableMap::COL_IS_ACTIVE, $modifiedColumns);
        $this->assertNotContains(SpyMerchantTableMap::COL_STATUS, $modifiedColumns);
    }

    public function testGivenChangedStoreRelationOnlyWhenUpdatingMerchantThenPublishEventContainsMerchantStoreColumn(): void
    {
        // Arrange
        $storeTransfer1 = $this->tester->haveStore([StoreTransfer::NAME => 'DE']);
        $storeTransfer2 = $this->tester->haveStore([StoreTransfer::NAME => 'AT']);
        $merchantTransfer = $this->tester->haveMerchantWithStore([StoreRelationTransfer::ID_STORES => [$storeTransfer1->getIdStoreOrFail()]]);
        $merchantTransfer->setStoreRelation(
            (new StoreRelationTransfer())->setIdStores([$storeTransfer1->getIdStoreOrFail(), $storeTransfer2->getIdStoreOrFail()]),
        );

        // Act
        $this->updateMerchant($merchantTransfer);

        // Assert
        $modifiedColumns = $this->getPublishEventModifiedColumns();
        $this->assertContains(SpyMerchantStoreTableMap::COL_FK_STORE, $modifiedColumns);
        $this->assertNotContains(SpyMerchantTableMap::COL_IS_ACTIVE, $modifiedColumns);
        $this->assertNotContains(MerchantConfig::MODIFIED_COLUMN_MERCHANT_RELATED_DATA, $modifiedColumns);
    }

    public function testGivenNoMerchantChangeWhenUpdatingMerchantThenPublishEventContainsRelatedDataMarkerOnly(): void
    {
        // Arrange
        $merchantTransfer = $this->tester->haveMerchant();

        // Act
        $this->updateMerchant($merchantTransfer);

        // Assert
        $this->assertSame([MerchantConfig::MODIFIED_COLUMN_MERCHANT_RELATED_DATA], $this->getPublishEventModifiedColumns());
    }

    public function testGivenPropertiesSetToStoredValuesWhenUpdatingMerchantThenPublishEventContainsRelatedDataMarkerOnly(): void
    {
        // Arrange
        $merchantTransfer = $this->tester->haveMerchant([MerchantTransfer::NAME => 'Same Company Name']);
        $merchantTransfer
            ->setName('Same Company Name')
            ->setIsActive($merchantTransfer->getIsActive())
            ->setStatus($merchantTransfer->getStatus());

        // Act
        $this->updateMerchant($merchantTransfer);

        // Assert
        $this->assertSame([MerchantConfig::MODIFIED_COLUMN_MERCHANT_RELATED_DATA], $this->getPublishEventModifiedColumns());
    }

    public function testGivenChangedColumnWithoutDedicatedHandlingWhenUpdatingMerchantThenPublishEventContainsThisColumn(): void
    {
        // Arrange
        $merchantTransfer = $this->tester->haveMerchant([MerchantTransfer::REGISTRATION_NUMBER => 'OLD-123']);
        $merchantTransfer->setRegistrationNumber('NEW-456');

        // Act
        $this->updateMerchant($merchantTransfer);

        // Assert
        $this->assertSame([SpyMerchantTableMap::COL_REGISTRATION_NUMBER], $this->getPublishEventModifiedColumns());
    }

    public function testGivenChangedIsActiveStatusAndStoresWhenUpdatingMerchantThenOnePublishEventContainsAllModifiedColumns(): void
    {
        // Arrange
        $storeTransfer1 = $this->tester->haveStore([StoreTransfer::NAME => 'DE']);
        $storeTransfer2 = $this->tester->haveStore([StoreTransfer::NAME => 'AT']);
        $merchantTransfer = $this->tester->haveMerchantWithStore([StoreRelationTransfer::ID_STORES => [$storeTransfer1->getIdStoreOrFail()]]);
        $merchantTransfer
            ->setIsActive(!$merchantTransfer->getIsActive())
            ->setStatus(MerchantConfig::STATUS_APPROVED)
            ->setStoreRelation(
                (new StoreRelationTransfer())->setIdStores([$storeTransfer1->getIdStoreOrFail(), $storeTransfer2->getIdStoreOrFail()]),
            );

        // Act
        $this->updateMerchant($merchantTransfer);

        // Assert
        $modifiedColumns = $this->getPublishEventModifiedColumns();
        $this->assertContains(SpyMerchantTableMap::COL_IS_ACTIVE, $modifiedColumns);
        $this->assertContains(SpyMerchantTableMap::COL_STATUS, $modifiedColumns);
        $this->assertContains(SpyMerchantStoreTableMap::COL_FK_STORE, $modifiedColumns);
        $this->assertNotContains(MerchantConfig::MODIFIED_COLUMN_MERCHANT_RELATED_DATA, $modifiedColumns);
    }

    /**
     * @dataProvider getForbiddenStatusTransitionsDataProvider
     */
    public function testGivenForbiddenStatusTransitionWhenUpdatingMerchantThenUpdateIsRejectedAndNoPublishEventIsTriggered(
        string $currentStatus,
        string $newStatus
    ): void {
        // Arrange
        $merchantTransfer = $this->tester->haveMerchant([MerchantTransfer::STATUS => $currentStatus]);
        $merchantTransfer->setStatus($newStatus);

        // Act
        $merchantResponseTransfer = $this->updateMerchant($merchantTransfer);

        // Assert
        $this->assertFalse($merchantResponseTransfer->getIsSuccess());
        $this->assertSame([], $this->publishEventEntityTransfers);
        $this->assertSame($currentStatus, $this->tester->getFacade()->findOne(
            (new MerchantCriteriaTransfer())->setIdMerchant($merchantTransfer->getIdMerchantOrFail()),
        )->getStatus());
    }

    /**
     * @return array<string, array<string>>
     */
    public function getForbiddenStatusTransitionsDataProvider(): array
    {
        $merchantConfig = new MerchantConfig();
        $statusTree = $merchantConfig->getStatusTree();
        $dataSets = [];

        foreach ($merchantConfig->getSupportedMerchantStatuses() as $currentStatus) {
            foreach ($merchantConfig->getSupportedMerchantStatuses() as $newStatus) {
                if ($newStatus === $currentStatus || in_array($newStatus, $statusTree[$currentStatus] ?? [], true)) {
                    continue;
                }

                $dataSets[sprintf('%s to %s', $currentStatus, $newStatus)] = [$currentStatus, $newStatus];
            }
        }

        return $dataSets;
    }

    protected function updateMerchant(MerchantTransfer $merchantTransfer): MerchantResponseTransfer
    {
        $this->publishEventEntityTransfers = [];

        return $this->tester->getFacade()->updateMerchant($merchantTransfer);
    }

    /**
     * @return array<string>
     */
    protected function getPublishEventModifiedColumns(): array
    {
        $this->assertCount(1, $this->publishEventEntityTransfers, 'Merchant publish event must be triggered exactly once.');

        return $this->publishEventEntityTransfers[0]->getModifiedColumns();
    }
}
