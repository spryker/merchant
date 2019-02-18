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
 * @group FindMerchantByIdMerchantTest
 * Add your own group annotations below this line
 */
class FindMerchantByIdMerchantTest extends AbstractMerchantFacadeTest
{
    /**
     * @return void
     */
    public function testFindMerchantByIdWillFindExistingMerchant(): void
    {
        $expectedMerchant = $this->tester->haveMerchantWithAddress();

        $actualMerchant = $this->tester->getFacade()->findMerchantByIdMerchant($expectedMerchant->getIdMerchant());

        $this->assertEquals($expectedMerchant, $actualMerchant);
    }

    /**
     * @return void
     */
    public function testFindMerchantByIdWillNotFindMerchant(): void
    {
        $merchantTransfer = $this->tester->haveMerchantWithAddress();

        $actualMerchant = $this->tester->getFacade()->findMerchantByIdMerchant($merchantTransfer->getIdMerchant() + 1);

        $this->assertNull($actualMerchant);
    }
}
