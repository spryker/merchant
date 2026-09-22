<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Merchant\Business\MerchantFacade;

use ArrayObject;
use Codeception\Test\Unit;
use Generated\Shared\Transfer\MerchantErrorTransfer;
use Generated\Shared\Transfer\MerchantResponseTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Spryker\Zed\Merchant\Communication\Plugin\Merchant\UniqueEmailMerchantValidatorPlugin;
use Spryker\Zed\Merchant\Communication\Plugin\Merchant\UniqueMerchantReferenceMerchantValidatorPlugin;
use Spryker\Zed\Merchant\Communication\Plugin\Merchant\UniqueNameMerchantValidatorPlugin;
use Spryker\Zed\Merchant\Communication\Plugin\Merchant\UrlMerchantValidatorPlugin;
use Spryker\Zed\Merchant\MerchantDependencyProvider;
use Spryker\Zed\MerchantExtension\Dependency\Plugin\MerchantValidatorPluginInterface;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Merchant
 * @group Business
 * @group MerchantFacade
 * @group ValidateMerchantTest
 * Add your own group annotations below this line
 */
class ValidateMerchantTest extends Unit
{
    protected const string ERROR_MESSAGE_EMAIL_IS_ALREADY_USED = 'Email is already used.';

    protected const string ERROR_MESSAGE_MERCHANT_REFERENCE_IS_ALREADY_USED = 'Merchant reference is already used.';

    protected const string ERROR_MESSAGE_NAME_IS_ALREADY_USED = 'Merchant name is already used.';

    protected const string ERROR_MESSAGE_FROM_PLUGIN = 'Rejected by the merchant validator plugin.';

    /**
     * @var \SprykerTest\Zed\Merchant\MerchantBusinessTester
     */
    protected $tester;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tester->setDependency(MerchantDependencyProvider::PLUGINS_MERCHANT_VALIDATOR, [
            new UniqueEmailMerchantValidatorPlugin(),
            new UniqueMerchantReferenceMerchantValidatorPlugin(),
            new UniqueNameMerchantValidatorPlugin(),
            new UrlMerchantValidatorPlugin(),
        ]);
    }

    public function testCreateMerchantRejectsAnEmailAnotherMerchantAlreadyUses(): void
    {
        // Arrange
        $existingMerchantTransfer = $this->tester->haveMerchant();
        $merchantTransfer = $this->tester->createMerchantTransfer()
            ->setEmail($existingMerchantTransfer->getEmailOrFail());

        // Act
        $merchantResponseTransfer = $this->tester->getFacade()->createMerchant($merchantTransfer);

        // Assert
        $this->assertFalse($merchantResponseTransfer->getIsSuccess());
        $this->assertSame(
            [static::ERROR_MESSAGE_EMAIL_IS_ALREADY_USED],
            $this->extractErrorMessages($merchantResponseTransfer),
        );
    }

    public function testCreateMerchantRejectsAMerchantReferenceAnotherMerchantAlreadyUses(): void
    {
        // Arrange
        $existingMerchantTransfer = $this->tester->haveMerchant();
        $merchantTransfer = $this->tester->createMerchantTransfer()
            ->setMerchantReference($existingMerchantTransfer->getMerchantReferenceOrFail());

        // Act
        $merchantResponseTransfer = $this->tester->getFacade()->createMerchant($merchantTransfer);

        // Assert
        $this->assertFalse($merchantResponseTransfer->getIsSuccess());
        $this->assertSame(
            [static::ERROR_MESSAGE_MERCHANT_REFERENCE_IS_ALREADY_USED],
            $this->extractErrorMessages($merchantResponseTransfer),
        );
    }

    public function testCreateMerchantRejectsANameAnotherMerchantAlreadyUses(): void
    {
        // Arrange
        $existingMerchantTransfer = $this->tester->haveMerchant();
        $merchantTransfer = $this->tester->createMerchantTransfer()
            ->setName($existingMerchantTransfer->getNameOrFail());

        // Act
        $merchantResponseTransfer = $this->tester->getFacade()->createMerchant($merchantTransfer);

        // Assert
        $this->assertFalse($merchantResponseTransfer->getIsSuccess());
        $this->assertSame(
            [static::ERROR_MESSAGE_NAME_IS_ALREADY_USED],
            $this->extractErrorMessages($merchantResponseTransfer),
        );
    }

    public function testUpdateMerchantAcceptsTheMerchantsOwnName(): void
    {
        // Arrange
        $merchantTransfer = $this->tester->haveMerchant();

        // Act
        $merchantResponseTransfer = $this->tester->getFacade()->updateMerchant($merchantTransfer);

        // Assert
        $this->assertTrue($merchantResponseTransfer->getIsSuccess());
    }

    public function testUpdateMerchantRejectsANameAnotherMerchantAlreadyUses(): void
    {
        // Arrange
        $otherMerchantTransfer = $this->tester->haveMerchant();
        $merchantTransfer = $this->tester->haveMerchant()
            ->setName($otherMerchantTransfer->getNameOrFail());

        // Act
        $merchantResponseTransfer = $this->tester->getFacade()->updateMerchant($merchantTransfer);

        // Assert
        $this->assertFalse($merchantResponseTransfer->getIsSuccess());
        $this->assertSame(
            [static::ERROR_MESSAGE_NAME_IS_ALREADY_USED],
            $this->extractErrorMessages($merchantResponseTransfer),
        );
    }

    public function testCreateMerchantDoesNotPersistTheRejectedMerchant(): void
    {
        // Arrange
        $existingMerchantTransfer = $this->tester->haveMerchant();
        $merchantTransfer = $this->tester->createMerchantTransfer()
            ->setEmail($existingMerchantTransfer->getEmailOrFail());

        // Act
        $merchantResponseTransfer = $this->tester->getFacade()->createMerchant($merchantTransfer);

        // Assert
        $this->assertNull($merchantResponseTransfer->getMerchantOrFail()->getIdMerchant());
    }

    public function testUpdateMerchantAcceptsTheMerchantsOwnEmailAndReference(): void
    {
        // Arrange
        $merchantTransfer = $this->tester->haveMerchant();

        // Act
        $merchantResponseTransfer = $this->tester->getFacade()->updateMerchant($merchantTransfer);

        // Assert
        $this->assertTrue($merchantResponseTransfer->getIsSuccess());
    }

    public function testUpdateMerchantRejectsAnEmailAnotherMerchantAlreadyUses(): void
    {
        // Arrange
        $otherMerchantTransfer = $this->tester->haveMerchant();
        $merchantTransfer = $this->tester->haveMerchant()
            ->setEmail($otherMerchantTransfer->getEmailOrFail());

        // Act
        $merchantResponseTransfer = $this->tester->getFacade()->updateMerchant($merchantTransfer);

        // Assert
        $this->assertFalse($merchantResponseTransfer->getIsSuccess());
        $this->assertSame(
            [static::ERROR_MESSAGE_EMAIL_IS_ALREADY_USED],
            $this->extractErrorMessages($merchantResponseTransfer),
        );
    }

    /**
     * Which locales require a URL is a channel-specific concern (the Back Office form always submits
     * one per locale; the Backend API enforces it per merchant store in
     * {@see \Spryker\Glue\Merchant\Api\Backend\Processor\MerchantsBackendProcessor}) - the domain layer
     * only validates the URLs it is actually given.
     */
    public function testCreateMerchantAcceptsAMerchantWithoutAnyUrl(): void
    {
        // Arrange
        $merchantTransfer = $this->tester->createMerchantTransfer()
            ->setUrlCollection(new ArrayObject());

        // Act
        $merchantResponseTransfer = $this->tester->getFacade()->createMerchant($merchantTransfer);

        // Assert
        $this->assertTrue($merchantResponseTransfer->getIsSuccess());
    }

    public function testCreateMerchantReportsEveryViolationAtOnce(): void
    {
        // Arrange
        $existingMerchantTransfer = $this->tester->haveMerchant();
        $merchantTransfer = $this->tester->createMerchantTransfer()
            ->setEmail($existingMerchantTransfer->getEmailOrFail())
            ->setMerchantReference($existingMerchantTransfer->getMerchantReferenceOrFail());

        // Act
        $merchantResponseTransfer = $this->tester->getFacade()->createMerchant($merchantTransfer);

        // Assert
        $this->assertSame(
            [
                static::ERROR_MESSAGE_EMAIL_IS_ALREADY_USED,
                static::ERROR_MESSAGE_MERCHANT_REFERENCE_IS_ALREADY_USED,
            ],
            $this->extractErrorMessages($merchantResponseTransfer),
        );
    }

    public function testCreateMerchantExecutesTheMerchantValidatorPluginStack(): void
    {
        // Arrange
        $this->tester->setDependency(
            MerchantDependencyProvider::PLUGINS_MERCHANT_VALIDATOR,
            [$this->createRejectingMerchantValidatorPlugin()],
        );
        $merchantTransfer = $this->tester->createMerchantTransfer();

        // Act
        $merchantResponseTransfer = $this->tester->getFacade()->createMerchant($merchantTransfer);

        // Assert
        $this->assertFalse($merchantResponseTransfer->getIsSuccess());
        $this->assertSame(
            [static::ERROR_MESSAGE_FROM_PLUGIN],
            $this->extractErrorMessages($merchantResponseTransfer),
        );
    }

    public function testUpdateMerchantExecutesTheMerchantValidatorPluginStack(): void
    {
        // Arrange
        $merchantTransfer = $this->tester->haveMerchant();
        $this->tester->setDependency(
            MerchantDependencyProvider::PLUGINS_MERCHANT_VALIDATOR,
            [$this->createRejectingMerchantValidatorPlugin()],
        );

        // Act
        $merchantResponseTransfer = $this->tester->getFacade()->updateMerchant($merchantTransfer);

        // Assert
        $this->assertFalse($merchantResponseTransfer->getIsSuccess());
        $this->assertSame(
            [static::ERROR_MESSAGE_FROM_PLUGIN],
            $this->extractErrorMessages($merchantResponseTransfer),
        );
    }

    protected function createRejectingMerchantValidatorPlugin(): MerchantValidatorPluginInterface
    {
        $errorMessage = static::ERROR_MESSAGE_FROM_PLUGIN;

        $merchantValidatorPluginMock = $this->getMockBuilder(MerchantValidatorPluginInterface::class)->getMock();
        $merchantValidatorPluginMock
            ->method('validate')
            ->willReturnCallback(static function (MerchantTransfer $merchantTransfer) use ($errorMessage): MerchantResponseTransfer {
                return (new MerchantResponseTransfer())
                    ->setIsSuccess(false)
                    ->setMerchant($merchantTransfer)
                    ->addError((new MerchantErrorTransfer())->setMessage($errorMessage));
            });

        return $merchantValidatorPluginMock;
    }

    /**
     * @return array<int, string>
     */
    protected function extractErrorMessages(MerchantResponseTransfer $merchantResponseTransfer): array
    {
        $messages = [];

        foreach ($merchantResponseTransfer->getErrors() as $merchantErrorTransfer) {
            $messages[] = $merchantErrorTransfer->getMessageOrFail();
        }

        return $messages;
    }
}
