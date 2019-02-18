<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Merchant\Business\MerchantFacade;

use SprykerTest\Zed\Merchant\Business\AbstractMerchantFacadeTest;

/**
 * Auto-generated group annotations
 * @group SprykerTest
 * @group Zed
 * @group Merchant
 * @group Business
 * @group MerchantFacade
 * @group FindMerchantAddressByIdMerchantAddressTest
 * Add your own group annotations below this line
 */
class FindMerchantAddressByIdMerchantAddressTest extends AbstractMerchantFacadeTest
{
    /**
     * @return void
     */
    public function testFindMerchantAddressByIdMerchantAddressWillFindMerchantAddress(): void
    {
        $expectedMerchantAddressTransfer = $this->tester->haveMerchantAddress();

        $actualMerchantAddressTransfer = $this->tester->getFacade()->findMerchantAddressByIdMerchantAddress(
            $expectedMerchantAddressTransfer->getIdMerchantAddress()
        );

        $this->assertNotEmpty($actualMerchantAddressTransfer->getIdMerchantAddress());
    }

    /**
     * @return void
     */
    public function testFindMerchantAddressByIdMerchantAddressWillNotFindMissingMerchantAddress(): void
    {
        $merchantAddressTransfer = $this->tester->haveMerchantAddress();

        $actualMerchantAddressTransfer = $this->tester->getFacade()->findMerchantAddressByIdMerchantAddress(
            $merchantAddressTransfer->getIdMerchantAddress() + 1
        );

        $this->assertNull($actualMerchantAddressTransfer);
    }
}