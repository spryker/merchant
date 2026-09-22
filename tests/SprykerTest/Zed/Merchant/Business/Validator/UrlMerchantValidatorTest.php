<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Merchant\Business\Validator;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\MerchantTransfer;
use Generated\Shared\Transfer\UrlTransfer;
use Spryker\Zed\Merchant\Business\Validator\UrlMerchantValidator;
use Spryker\Zed\Merchant\Dependency\Facade\MerchantToUrlFacadeInterface;
use Spryker\Zed\Merchant\MerchantConfig;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Merchant
 * @group Business
 * @group Validator
 * @group UrlMerchantValidatorTest
 * Add your own group annotations below this line
 */
class UrlMerchantValidatorTest extends Unit
{
    protected const string MERCHANT_URL = '/de/merchant/spryker';

    protected const int ID_MERCHANT = 1;

    protected const int ID_OTHER_MERCHANT = 2;

    protected const string LOCALE_NAME = 'de_DE';

    /**
     * @var \SprykerTest\Zed\Merchant\MerchantBusinessTester
     */
    protected $tester;

    public function testValidateAcceptsAUrlNobodyOwnsYet(): void
    {
        // Arrange
        $urlMerchantValidator = $this->createUrlMerchantValidator($this->createUrlFacadeMock(null));

        // Act
        $merchantResponseTransfer = $urlMerchantValidator->validate($this->createMerchantTransferWithUrl(null));

        // Assert
        $this->assertTrue($merchantResponseTransfer->getIsSuccess());
        $this->assertCount(0, $merchantResponseTransfer->getErrors());
    }

    public function testValidateAcceptsAUrlTheMerchantUnderValidationAlreadyOwns(): void
    {
        // Arrange
        $existingUrlTransfer = (new UrlTransfer())
            ->setUrl(static::MERCHANT_URL)
            ->setFkResourceMerchant(static::ID_MERCHANT);
        $urlMerchantValidator = $this->createUrlMerchantValidator($this->createUrlFacadeMock($existingUrlTransfer));

        // Act
        $merchantResponseTransfer = $urlMerchantValidator->validate(
            $this->createMerchantTransferWithUrl(static::ID_MERCHANT),
        );

        // Assert
        $this->assertTrue($merchantResponseTransfer->getIsSuccess());
    }

    public function testValidateRejectsAUrlAnotherMerchantOwns(): void
    {
        // Arrange
        $existingUrlTransfer = (new UrlTransfer())
            ->setUrl(static::MERCHANT_URL)
            ->setFkResourceMerchant(static::ID_OTHER_MERCHANT);
        $urlMerchantValidator = $this->createUrlMerchantValidator($this->createUrlFacadeMock($existingUrlTransfer));

        // Act
        $merchantResponseTransfer = $urlMerchantValidator->validate(
            $this->createMerchantTransferWithUrl(static::ID_MERCHANT),
        );

        // Assert
        $this->assertFalse($merchantResponseTransfer->getIsSuccess());
        $this->assertStringContainsString(
            static::MERCHANT_URL,
            $merchantResponseTransfer->getErrors()->offsetGet(0)->getMessageOrFail(),
        );
    }

    public function testValidateRejectsAUrlOwnedByANonMerchantResource(): void
    {
        // Arrange
        $existingUrlTransfer = (new UrlTransfer())->setUrl(static::MERCHANT_URL);
        $urlMerchantValidator = $this->createUrlMerchantValidator($this->createUrlFacadeMock($existingUrlTransfer));

        // Act
        $merchantResponseTransfer = $urlMerchantValidator->validate($this->createMerchantTransferWithUrl(null));

        // Assert
        $this->assertFalse($merchantResponseTransfer->getIsSuccess());
    }

    public function testValidateRejectsABlankUrl(): void
    {
        // Arrange
        $merchantTransfer = (new MerchantTransfer())
            ->setIdMerchant(static::ID_MERCHANT)
            ->addUrl((new UrlTransfer())->setUrl('')->setLocaleName(static::LOCALE_NAME));
        $urlMerchantValidator = $this->createUrlMerchantValidator($this->createUrlFacadeMock(null));

        // Act
        $merchantResponseTransfer = $urlMerchantValidator->validate($merchantTransfer);

        // Assert
        $this->assertFalse($merchantResponseTransfer->getIsSuccess());
        $this->assertStringContainsString(
            static::LOCALE_NAME,
            $merchantResponseTransfer->getErrors()->offsetGet(0)->getMessageOrFail(),
        );
    }

    protected function createMerchantTransferWithUrl(?int $idMerchant): MerchantTransfer
    {
        return (new MerchantTransfer())
            ->setIdMerchant($idMerchant)
            ->addUrl((new UrlTransfer())->setUrl(static::MERCHANT_URL));
    }

    protected function createUrlMerchantValidator(
        MerchantToUrlFacadeInterface $urlFacade
    ): UrlMerchantValidator {
        return new UrlMerchantValidator($urlFacade, new MerchantConfig());
    }

    protected function createUrlFacadeMock(?UrlTransfer $existingUrlTransfer): MerchantToUrlFacadeInterface
    {
        $urlFacadeMock = $this->getMockBuilder(MerchantToUrlFacadeInterface::class)->getMock();
        $urlFacadeMock->method('findUrlCaseInsensitive')->willReturn($existingUrlTransfer);

        return $urlFacadeMock;
    }
}
