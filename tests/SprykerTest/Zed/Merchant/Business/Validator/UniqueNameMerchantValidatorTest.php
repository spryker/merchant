<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Merchant\Business\Validator;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\MerchantTransfer;
use Spryker\Zed\Merchant\Business\Validator\UniqueNameMerchantValidator;
use Spryker\Zed\Merchant\Persistence\MerchantRepositoryInterface;
use SprykerTest\Zed\Merchant\MerchantBusinessTester;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Merchant
 * @group Business
 * @group Validator
 * @group UniqueNameMerchantValidatorTest
 * Add your own group annotations below this line
 */
class UniqueNameMerchantValidatorTest extends Unit
{
    protected const string MERCHANT_NAME = 'Spryker Merchant';

    protected const int ID_MERCHANT = 1;

    protected const int ID_OTHER_MERCHANT = 2;

    protected MerchantBusinessTester $tester;

    public function testValidateAcceptsANameNobodyOwnsYet(): void
    {
        // Arrange
        $uniqueNameMerchantValidator = $this->createUniqueNameMerchantValidator($this->createMerchantRepositoryMock(null));

        // Act
        $merchantResponseTransfer = $uniqueNameMerchantValidator->validate($this->createMerchantTransferWithName(null));

        // Assert
        $this->assertTrue($merchantResponseTransfer->getIsSuccess());
        $this->assertCount(0, $merchantResponseTransfer->getErrors());
    }

    public function testValidateAcceptsANameTheMerchantUnderValidationAlreadyOwns(): void
    {
        // Arrange
        $existingMerchantTransfer = (new MerchantTransfer())
            ->setIdMerchant(static::ID_MERCHANT)
            ->setName(static::MERCHANT_NAME);
        $uniqueNameMerchantValidator = $this->createUniqueNameMerchantValidator(
            $this->createMerchantRepositoryMock($existingMerchantTransfer),
        );

        // Act
        $merchantResponseTransfer = $uniqueNameMerchantValidator->validate(
            $this->createMerchantTransferWithName(static::ID_MERCHANT),
        );

        // Assert
        $this->assertTrue($merchantResponseTransfer->getIsSuccess());
    }

    public function testValidateRejectsANameAnotherMerchantOwns(): void
    {
        // Arrange
        $existingMerchantTransfer = (new MerchantTransfer())
            ->setIdMerchant(static::ID_OTHER_MERCHANT)
            ->setName(static::MERCHANT_NAME);
        $uniqueNameMerchantValidator = $this->createUniqueNameMerchantValidator(
            $this->createMerchantRepositoryMock($existingMerchantTransfer),
        );

        // Act
        $merchantResponseTransfer = $uniqueNameMerchantValidator->validate(
            $this->createMerchantTransferWithName(static::ID_MERCHANT),
        );

        // Assert
        $this->assertFalse($merchantResponseTransfer->getIsSuccess());
        $this->assertStringContainsString(
            'name',
            $merchantResponseTransfer->getErrors()->offsetGet(0)->getMessageOrFail(),
        );
    }

    public function testValidateAcceptsABlankName(): void
    {
        // Arrange
        $uniqueNameMerchantValidator = $this->createUniqueNameMerchantValidator($this->createMerchantRepositoryMock(null));

        // Act
        $merchantResponseTransfer = $uniqueNameMerchantValidator->validate($this->createMerchantTransferWithName(null, ''));

        // Assert
        $this->assertTrue($merchantResponseTransfer->getIsSuccess());
    }

    protected function createMerchantTransferWithName(?int $idMerchant, string $name = self::MERCHANT_NAME): MerchantTransfer
    {
        return (new MerchantTransfer())
            ->setIdMerchant($idMerchant)
            ->setName($name);
    }

    protected function createUniqueNameMerchantValidator(
        MerchantRepositoryInterface $merchantRepository
    ): UniqueNameMerchantValidator {
        return new UniqueNameMerchantValidator($merchantRepository);
    }

    protected function createMerchantRepositoryMock(?MerchantTransfer $existingMerchantTransfer): MerchantRepositoryInterface
    {
        $merchantRepositoryMock = $this->getMockBuilder(MerchantRepositoryInterface::class)->getMock();
        $merchantRepositoryMock->method('findOne')->willReturn($existingMerchantTransfer);

        return $merchantRepositoryMock;
    }
}
