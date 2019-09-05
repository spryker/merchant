<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Merchant\Business\MerchantFacade;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\MerchantResponseTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Spryker\Shared\Kernel\Transfer\Exception\RequiredTransferPropertyException;
use Spryker\Zed\Merchant\MerchantConfig;

/**
 * Auto-generated group annotations
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

    /**
     * @return void
     */
    public function testUpdateMerchant(): void
    {
        // Arrange
        $merchantTransfer = $this->tester->haveMerchant([
            'one-key',
            'One Company',
        ]);

        $expectedIdMerchant = $merchantTransfer->getIdMerchant();
        $merchantTransfer
            ->setMerchantKey('second-key')
            ->setName('Second Company');

        // Act
        $merchantResponseTransfer = $this->tester->getFacade()->updateMerchant($merchantTransfer);
        $updatedMerchant = $merchantResponseTransfer->getMerchant();

        // Assert
        $this->assertSame($expectedIdMerchant, $updatedMerchant->getIdMerchant());
        $this->assertEquals('second-key', $updatedMerchant->getMerchantKey());
        $this->assertEquals('Second Company', $updatedMerchant->getName());
        $this->assertNotEmpty($updatedMerchant->getAddressCollection()->getAddresses()->offsetGet(0));
    }

    /**
     * @dataProvider getCorrectStatusTransitions
     *
     * @param string[] $presetStatuses
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
     * @param string[] $presetStatuses
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

    /**
     * @return void
     */
    public function testUpdateMerchantWithEmptyRequiredFieldsThrowsException(): void
    {
        // Arrange
        $merchantTransfer = $this->tester->createMerchantTransferWithAddressTransfer();
        $merchantWithEmptyNameTransfer = clone $merchantTransfer;
        $merchantWithEmptyNameTransfer->setName(null);

        $merchantWithEmptyRegistrationNumberTransfer = clone $merchantTransfer;
        $merchantWithEmptyRegistrationNumberTransfer->setRegistrationNumber(null);
        $merchantWithEmptyContactPersonTitleTransfer = clone $merchantTransfer;
        $merchantWithEmptyContactPersonTitleTransfer->setContactPersonTitle(null);
        $merchantWithEmptyContactPersonFirstNameTransfer = clone $merchantTransfer;
        $merchantWithEmptyContactPersonFirstNameTransfer->setContactPersonFirstName(null);
        $merchantWithEmptyContactPersonLastNameTransfer = clone $merchantTransfer;
        $merchantWithEmptyContactPersonLastNameTransfer->setContactPersonLastName(null);
        $merchantWithEmptyContactPersonPhoneTransfer = clone $merchantTransfer;
        $merchantWithEmptyContactPersonPhoneTransfer->setContactPersonPhone(null);
        $merchantWithEmptyEmailTransfer = clone $merchantTransfer;
        $merchantWithEmptyEmailTransfer->setEmail(null);
        $merchantTransfer->setIdMerchant(null);

        // Assert
        $this->expectException(RequiredTransferPropertyException::class);

        // Act
        $this->tester->getFacade()->createMerchant($merchantWithEmptyNameTransfer);
        $this->tester->getFacade()->createMerchant($merchantWithEmptyRegistrationNumberTransfer);
        $this->tester->getFacade()->createMerchant($merchantWithEmptyContactPersonTitleTransfer);
        $this->tester->getFacade()->createMerchant($merchantWithEmptyContactPersonFirstNameTransfer);
        $this->tester->getFacade()->createMerchant($merchantWithEmptyContactPersonLastNameTransfer);
        $this->tester->getFacade()->createMerchant($merchantWithEmptyContactPersonPhoneTransfer);
        $this->tester->getFacade()->createMerchant($merchantWithEmptyEmailTransfer);
        $this->tester->getFacade()->createMerchant($merchantTransfer);
    }

    /**
     * @return void
     */
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

    /**
     * @return array
     */
    public function getCorrectStatusTransitions(): array
    {
        return [
            [[MerchantConfig::STATUS_APPROVED]],
            [[MerchantConfig::STATUS_APPROVED, MerchantConfig::STATUS_ACTIVE]],
            [[MerchantConfig::STATUS_APPROVED, MerchantConfig::STATUS_INACTIVE]],
            [[MerchantConfig::STATUS_APPROVED, MerchantConfig::STATUS_ACTIVE, MerchantConfig::STATUS_INACTIVE]],
            [[MerchantConfig::STATUS_APPROVED, MerchantConfig::STATUS_INACTIVE, MerchantConfig::STATUS_ACTIVE]],
        ];
    }

    /**
     * @return array
     */
    public function getWrongStatusTransitions(): array
    {
        return [
            [[MerchantConfig::STATUS_ACTIVE]],
            [[MerchantConfig::STATUS_INACTIVE]],
            [[MerchantConfig::STATUS_APPROVED, MerchantConfig::STATUS_WAITING_FOR_APPROVAL]],
            [[MerchantConfig::STATUS_APPROVED, MerchantConfig::STATUS_ACTIVE, MerchantConfig::STATUS_WAITING_FOR_APPROVAL]],
            [[MerchantConfig::STATUS_APPROVED, MerchantConfig::STATUS_INACTIVE, MerchantConfig::STATUS_WAITING_FOR_APPROVAL]],
        ];
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     * @param array $presetStatuses
     *
     * @return \Generated\Shared\Transfer\MerchantResponseTransfer
     */
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
