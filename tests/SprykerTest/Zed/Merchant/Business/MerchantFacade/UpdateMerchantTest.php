<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Merchant\Business\MerchantFacade;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\MerchantResponseTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Generated\Shared\Transfer\StoreRelationTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use Spryker\Shared\Kernel\Transfer\Exception\RequiredTransferPropertyException;
use Spryker\Zed\Merchant\MerchantConfig;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Merchant
 * @group Business
 * @group MerchantFacade
 * @group UpdateMerchantTest
 * Add your own group annotations below this line
 */
class UpdateMerchantTest extends Unit
{
    /**
     * @var \SprykerTest\Zed\Merchant\MerchantBusinessTester
     */
    protected $tester;

    public function testUpdateMerchant(): void
    {
        // Arrange
        $merchantTransfer = $this->tester->haveMerchant([
            MerchantTransfer::MERCHANT_REFERENCE => 'one-reference',
            MerchantTransfer::NAME => 'One Company',
        ]);

        $expectedIdMerchant = $merchantTransfer->getIdMerchant();
        $merchantTransfer
            ->setMerchantReference('second-reference')
            ->setName('Second Company');

        // Act
        $merchantResponseTransfer = $this->tester->getFacade()->updateMerchant($merchantTransfer);
        $updatedMerchant = $merchantResponseTransfer->getMerchant();

        // Assert
        $this->assertSame($expectedIdMerchant, $updatedMerchant->getIdMerchant());
        $this->assertSame('second-reference', $updatedMerchant->getMerchantReference());
        $this->assertSame('Second Company', $updatedMerchant->getName());
    }

    /**
     * @dataProvider getCorrectStatusTransitions
     *
     * @param array<string> $presetStatuses
     *
     * @return void
     */
    public function testUpdateMerchantWithCorrectStatusWorks(array $presetStatuses): void
    {
        // Arrange
        $merchantTransfer = $this->tester->haveMerchant();
        $expectedStatus = end($presetStatuses);

        // Act
        $merchantResponseTransfer = $this->updateMerchantWithStatuses($merchantTransfer, $presetStatuses);

        // Assert
        $this->assertTrue($merchantResponseTransfer->getIsSuccess());
        $this->assertSame($expectedStatus, $merchantResponseTransfer->getMerchant()->getStatus());
    }

    /**
     * @dataProvider getWrongStatusTransitions
     *
     * @param array<string> $presetStatuses
     *
     * @return void
     */
    public function testUpdateMerchantWithIncorrectStatusReturnsIsSuccessFalse(array $presetStatuses): void
    {
        // Arrange
        $merchantTransfer = $this->tester->haveMerchant();

        // Act
        $merchantResponseTransfer = $this->updateMerchantWithStatuses($merchantTransfer, $presetStatuses);

        // Assert
        $this->assertFalse($merchantResponseTransfer->getIsSuccess());
    }

    public function testUpdateMerchantWithEmptyRequiredFieldsThrowsException(): void
    {
        // Arrange
        $merchantTransfer = $this->tester->createMerchantTransfer();
        $merchantWithEmptyNameTransfer = clone $merchantTransfer;
        $merchantWithEmptyNameTransfer->setName(null);

        $merchantWithEmptyEmailTransfer = clone $merchantTransfer;
        $merchantWithEmptyEmailTransfer->setEmail(null);
        $merchantTransfer->setIdMerchant(null);

        // Assert
        $this->expectException(RequiredTransferPropertyException::class);

        // Act
        $this->tester->getFacade()->updateMerchant($merchantWithEmptyNameTransfer);
        $this->tester->getFacade()->updateMerchant($merchantWithEmptyEmailTransfer);
        $this->tester->getFacade()->updateMerchant($merchantTransfer);
    }

    public function testUpdateMerchantWithWrongIdReturnsIsSuccessFalse(): void
    {
        // Arrange
        $merchantTransfer = $this->tester->haveMerchant();
        $merchantTransfer
            ->setIdMerchant($merchantTransfer->getIdMerchant() + 1);

        // Act
        $merchantResponseTransfer = $this->tester->getFacade()->updateMerchant($merchantTransfer);

        // Assert
        $this->assertFalse($merchantResponseTransfer->getIsSuccess());
    }

    public function testGivenAnAdditionalStoreWhenUpdatingMerchantThenBothStoresAreAssigned(): void
    {
        // Arrange
        $storeTransfer1 = $this->tester->haveStore([StoreTransfer::NAME => 'DE']);
        $storeTransfer2 = $this->tester->haveStore([StoreTransfer::NAME => 'AT']);
        $merchantTransfer = $this->tester->haveMerchantWithStore([StoreRelationTransfer::ID_STORES => [$storeTransfer1->getIdStore()]]);
        $storesIds = [
            $storeTransfer1->getIdStore(),
            $storeTransfer2->getIdStore(),
        ];
        $merchantTransfer->getStoreRelation()->setIdStores($storesIds);

        // Act
        $merchantResponseTransfer = $this->tester->getFacade()->updateMerchant($merchantTransfer);
        $merchantTransfer = $merchantResponseTransfer->getMerchant();

        // Assert
        $this->assertEqualsCanonicalizing($storesIds, $merchantTransfer->getStoreRelation()->getIdStores());
        $this->assertEqualsCanonicalizing($storesIds, $this->getStoreIds($merchantTransfer->getStoreRelation()));
    }

    public function testGivenUnchangedStoresWhenUpdatingMerchantThenTheCurrentStoreRelationIsReturned(): void
    {
        // Arrange
        $storeTransfer = $this->tester->haveStore([StoreTransfer::NAME => 'DE']);
        $merchantTransfer = $this->tester->haveMerchantWithStore([StoreRelationTransfer::ID_STORES => [$storeTransfer->getIdStoreOrFail()]]);
        $merchantTransfer->setStoreRelation(
            (new StoreRelationTransfer())->setIdStores([$storeTransfer->getIdStoreOrFail()]),
        );

        // Act
        $merchantResponseTransfer = $this->tester->getFacade()->updateMerchant($merchantTransfer);

        // Assert
        $storeRelationTransfer = $merchantResponseTransfer->getMerchantOrFail()->getStoreRelationOrFail();
        $this->assertTrue($merchantResponseTransfer->getIsSuccess());
        $this->assertSame([$storeTransfer->getIdStoreOrFail()], $storeRelationTransfer->getIdStores());
        $this->assertSame([$storeTransfer->getIdStoreOrFail()], $this->getStoreIds($storeRelationTransfer));
    }

    public function testGivenADuplicatedStoreIdWhenUpdatingMerchantThenTheStoreIsAssignedOnce(): void
    {
        // Arrange
        $storeTransfer1 = $this->tester->haveStore([StoreTransfer::NAME => 'DE']);
        $storeTransfer2 = $this->tester->haveStore([StoreTransfer::NAME => 'AT']);
        $merchantTransfer = $this->tester->haveMerchantWithStore([StoreRelationTransfer::ID_STORES => [$storeTransfer1->getIdStoreOrFail()]]);
        $merchantTransfer->setStoreRelation((new StoreRelationTransfer())->setIdStores([
            $storeTransfer1->getIdStoreOrFail(),
            $storeTransfer2->getIdStoreOrFail(),
            $storeTransfer2->getIdStoreOrFail(),
        ]));

        // Act
        $merchantResponseTransfer = $this->tester->getFacade()->updateMerchant($merchantTransfer);

        // Assert
        $storeRelationTransfer = $merchantResponseTransfer->getMerchantOrFail()->getStoreRelationOrFail();
        $expectedStoreIds = [$storeTransfer1->getIdStoreOrFail(), $storeTransfer2->getIdStoreOrFail()];
        $this->assertTrue($merchantResponseTransfer->getIsSuccess());
        $this->assertEqualsCanonicalizing($expectedStoreIds, $storeRelationTransfer->getIdStores());
        $this->assertEqualsCanonicalizing($expectedStoreIds, $this->getStoreIds($storeRelationTransfer));
    }

    public function testGivenARemovedStoreWhenUpdatingMerchantThenTheRemainingStoreRelationIsReturned(): void
    {
        // Arrange
        $storeTransfer1 = $this->tester->haveStore([StoreTransfer::NAME => 'DE']);
        $storeTransfer2 = $this->tester->haveStore([StoreTransfer::NAME => 'AT']);
        $merchantTransfer = $this->tester->haveMerchantWithStore([
            StoreRelationTransfer::ID_STORES => [
                $storeTransfer1->getIdStoreOrFail(),
                $storeTransfer2->getIdStoreOrFail(),
            ],
        ]);
        $merchantTransfer->setStoreRelation(
            (new StoreRelationTransfer())->setIdStores([$storeTransfer1->getIdStoreOrFail()]),
        );

        // Act
        $merchantResponseTransfer = $this->tester->getFacade()->updateMerchant($merchantTransfer);

        // Assert
        $storeRelationTransfer = $merchantResponseTransfer->getMerchantOrFail()->getStoreRelationOrFail();
        $this->assertSame([$storeTransfer1->getIdStoreOrFail()], $storeRelationTransfer->getIdStores());
        $this->assertSame([$storeTransfer1->getIdStoreOrFail()], $this->getStoreIds($storeRelationTransfer));
    }

    /**
     * @return array<int>
     */
    protected function getStoreIds(StoreRelationTransfer $storeRelationTransfer): array
    {
        return array_map(
            fn (StoreTransfer $storeTransfer): int => $storeTransfer->getIdStoreOrFail(),
            $storeRelationTransfer->getStores()->getArrayCopy(),
        );
    }

    public function getCorrectStatusTransitions(): array
    {
        return [
            [[MerchantConfig::STATUS_APPROVED]],
            [[MerchantConfig::STATUS_APPROVED, MerchantConfig::STATUS_DENIED]],
            [[MerchantConfig::STATUS_APPROVED, MerchantConfig::STATUS_DENIED, MerchantConfig::STATUS_APPROVED]],
        ];
    }

    public function getWrongStatusTransitions(): array
    {
        return [
            [[MerchantConfig::STATUS_APPROVED, MerchantConfig::STATUS_WAITING_FOR_APPROVAL]],
            [[MerchantConfig::STATUS_DENIED, MerchantConfig::STATUS_WAITING_FOR_APPROVAL]],
        ];
    }

    protected function updateMerchantWithStatuses(MerchantTransfer $merchantTransfer, array $presetStatuses): MerchantResponseTransfer
    {
        $merchantResponseTransfer = (new MerchantResponseTransfer())->setIsSuccess(false);

        foreach ($presetStatuses as $presetStatus) {
            $merchantTransfer->setStatus($presetStatus);
            $merchantResponseTransfer = $this->tester->getFacade()->updateMerchant($merchantTransfer);
            $merchantTransfer = $merchantResponseTransfer->getMerchant();

            if ($merchantResponseTransfer->getIsSuccess() === false) {
                return $merchantResponseTransfer;
            }
        }

        return $merchantResponseTransfer;
    }
}
