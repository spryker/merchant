<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Merchant\Business\MerchantFacade;

use Spryker\Shared\Kernel\Transfer\Exception\RequiredTransferPropertyException;
use SprykerTest\Zed\Merchant\Business\AbstractMerchantFacadeTest;

/**
 * Auto-generated group annotations
 * @group SprykerTest
 * @group Zed
 * @group Merchant
 * @group Business
 * @group MerchantFacade
 * @group CreateMerchantTest
 * Add your own group annotations below this line
 */
class CreateMerchantTest extends AbstractMerchantFacadeTest
{
    /**
     * @return void
     */
    public function testCreateMerchant(): void
    {
        $merchantTransfer = $this->tester->createMerchantTransferWithAddressTransfer();

        $merchantResponseTransfer = $this->tester->getFacade()->createMerchant($merchantTransfer);

        $this->assertNotNull($merchantResponseTransfer->getMerchant()->getIdMerchant());
    }

    /**
     * @return void
     */
    public function testCreateMerchantCreatesMerchantWithCorrectStatus(): void
    {
        $merchantTransfer = $this->tester->createMerchantTransferWithAddressTransfer();

        $merchantResponseTransfer = $this->tester->getFacade()->createMerchant($merchantTransfer);

        $this->assertEquals($this->tester->createMerchantConfig()->getDefaultMerchantStatus(), $merchantResponseTransfer->getMerchant()->getStatus());
    }

    /**
     * @return void
     */
    public function testCreateMerchantWithEmptyNameThrowsException(): void
    {
        $merchantTransfer = $this->tester->createMerchantTransferWithAddressTransfer()
            ->setName(null);

        $this->expectException(RequiredTransferPropertyException::class);

        $this->tester->getFacade()->createMerchant($merchantTransfer);
    }

    /**
     * @return void
     */
    public function testCreateMerchantWithNotUniqueName(): void
    {
        $merchantTransfer = $this->tester->haveMerchantWithAddress();
        $newMerchantTransfer = $this->tester->createMerchantTransferWithAddressTransfer()
            ->setName($merchantTransfer->getName());

        $merchantResponseTransfer = $this->tester->getFacade()->createMerchant($newMerchantTransfer);
        $this->assertNotNull($merchantResponseTransfer->getMerchant()->getIdMerchant());
    }
}
