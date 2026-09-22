<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Merchant\Business\MerchantFacade;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\FilterTransfer;
use Generated\Shared\Transfer\MerchantCollectionTransfer;
use Generated\Shared\Transfer\MerchantCriteriaTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Generated\Shared\Transfer\PaginationTransfer;
use Generated\Shared\Transfer\StoreRelationTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use Orm\Zed\Merchant\Persistence\Map\SpyMerchantTableMap;
use Spryker\Zed\Merchant\MerchantConfig;
use Spryker\Zed\Merchant\MerchantDependencyProvider;
use Spryker\Zed\MerchantExtension\Dependency\Plugin\MerchantBulkExpanderPluginInterface;
use Spryker\Zed\MerchantExtension\Dependency\Plugin\MerchantExpanderPluginInterface;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Merchant
 * @group Business
 * @group MerchantFacade
 * @group GetMerchantDataTest
 * Add your own group annotations below this line
 */
class GetMerchantDataTest extends Unit
{
    protected const string STORE_NAME_FIRST = 'DE';

    protected const string STORE_NAME_SECOND = 'AT';

    protected const string SEARCHABLE_MERCHANT_NAME = 'Spryker Searchable Merchant';

    protected const string NON_MATCHING_MERCHANT_NAME = 'Unrelated Merchant';

    protected const string SEARCH_TERM_IN_DIFFERENT_CASE = 'sEaRcHaBlE';

    /**
     * @var \SprykerTest\Zed\Merchant\MerchantBusinessTester
     */
    protected $tester;

    public function testFindOneMerchant(): void
    {
        // Arrange
        $expectedMerchant = $this->tester->haveMerchant();

        // Act
        $merchantCriteriaTransfer = new MerchantCriteriaTransfer();
        $merchantCriteriaTransfer->setIdMerchant($expectedMerchant->getIdMerchant());

        $actualMerchantById = $this->tester->getFacade()->findOne($merchantCriteriaTransfer);

        $merchantCriteriaTransfer = new MerchantCriteriaTransfer();
        $merchantCriteriaTransfer->setEmail($expectedMerchant->getEmail());

        $actualMerchantByEmail = $this->tester->getFacade()->findOne($merchantCriteriaTransfer);

        $this->assertEquals($expectedMerchant, $actualMerchantById);
        $this->assertEquals($expectedMerchant, $actualMerchantByEmail);
    }

    public function testFindOneMerchantExecutesExpanderPluginsStack(): void
    {
        // Arrange
        $merchant = $this->tester->haveMerchant();
        $merchantCriteriaTransfer = (new MerchantCriteriaTransfer())
            ->setIdMerchant($merchant->getIdMerchant());
        $merchantCollection = (new MerchantCollectionTransfer())
            ->addMerchants($merchant);

        // Assert
        $merchantExpanderPluginMock = $this
            ->getMockBuilder(MerchantExpanderPluginInterface::class)
            ->getMock();

        $merchantExpanderPluginMock
            ->expects($this->once())
            ->method('expand');

        $this->tester->setDependency(MerchantDependencyProvider::PLUGINS_MERCHANT_EXPANDER, [$merchantExpanderPluginMock]);

        $merchantBulkExpanderPluginMock = $this
            ->getMockBuilder(MerchantBulkExpanderPluginInterface::class)
            ->getMock();

        $merchantBulkExpanderPluginMock
            ->expects($this->once())
            ->method('expand')
            ->willReturn($merchantCollection);

        $this->tester->setDependency(MerchantDependencyProvider::PLUGINS_MERCHANT_BULK_EXPANDER, [$merchantBulkExpanderPluginMock]);

        // Act
        $this->tester->getFacade()
            ->findOne($merchantCriteriaTransfer);
    }

    public function testFindMerchantByIdWillNotFindMerchant(): void
    {
        // Arrange
        $merchantTransfer = $this->tester->haveMerchant();

        $merchantCriteriaTransfer = new MerchantCriteriaTransfer();
        $merchantCriteriaTransfer->setIdMerchant($merchantTransfer->getIdMerchant() + 1);

        // Act
        $actualMerchant = $this->tester->getFacade()->findOne($merchantCriteriaTransfer);

        // Assert
        $this->assertNull($actualMerchant);
    }

    public function testFindMerchants(): void
    {
        // Arrange
        $this->tester->truncateMerchantRelations();

        $this->tester->haveMerchant();
        $this->tester->haveMerchant();

        // Act
        $merchantCriteriaTransfer = new MerchantCriteriaTransfer();
        $merchantCollectionWithoutCriteriaTransfer = $this->tester->getFacade()->get($merchantCriteriaTransfer);

        // Arrange
        $paginationTransfer = new PaginationTransfer();
        $paginationTransfer->setPage(1);
        $paginationTransfer->setMaxPerPage(1);
        $merchantCriteriaTransfer->setPagination($paginationTransfer);

        // Act
        $merchantCollectionWithPaginationTransfer = $this->tester->getFacade()->get($merchantCriteriaTransfer);

        // Arrange
        $paginationTransfer = new FilterTransfer();
        $paginationTransfer->setOrderBy(SpyMerchantTableMap::COL_ID_MERCHANT);
        $paginationTransfer->setOrderDirection('DESC');
        $merchantCriteriaTransfer->setFilter($paginationTransfer);

        // Act
        $merchantCollectionWithPaginationTransfer = $this->tester->getFacade()->get($merchantCriteriaTransfer);

        // Arrange
        $merchantCriteriaTransfer->getFilter()->setOrderDirection('ASC');

        // Act
        $merchantCollectionOrderByNameDescTransfer = $this->tester->getFacade()->get($merchantCriteriaTransfer);

        // Assert
        $this->assertCount(2, $merchantCollectionWithoutCriteriaTransfer->getMerchants());
        $this->assertCount(1, $merchantCollectionWithPaginationTransfer->getMerchants());
        $this->assertCount(1, $merchantCollectionOrderByNameDescTransfer->getMerchants());

        $this->assertNotEquals(
            $merchantCollectionOrderByNameDescTransfer->getMerchants()[0]->getIdMerchant(),
            $merchantCollectionWithPaginationTransfer->getMerchants()[0]->getIdMerchant(),
        );
    }

    public function testGetApplicableMerchantStatusesWillReturnArray(): void
    {
        // Act
        $applicableMerchantStatuses = $this->tester->getFacade()->getApplicableMerchantStatuses($this->tester->createMerchantConfig()->getDefaultMerchantStatus());

        // Assert
        $this->assertTrue(is_array($applicableMerchantStatuses));
        $this->assertNotEmpty($applicableMerchantStatuses);
    }

    public function testGetApplicableMerchantStatusesWillReturnEmptyArrayOnNotFoundCurrentStatus(): void
    {
        // Act
        $applicableMerchantStatuses = $this->tester->getFacade()->getApplicableMerchantStatuses('random-status');

        // Assert
        $this->assertTrue(is_array($applicableMerchantStatuses));
        $this->assertEmpty($applicableMerchantStatuses);
    }

    public function testGetReturnsThePaginationOfTheExpandedCollection(): void
    {
        // Arrange
        $this->tester->truncateMerchantRelations();
        $this->tester->haveMerchant();
        $this->tester->haveMerchant();

        $merchantCriteriaTransfer = (new MerchantCriteriaTransfer())
            ->setPagination((new PaginationTransfer())->setPage(1)->setMaxPerPage(1));

        // Act
        $merchantCollectionTransfer = $this->tester->getFacade()->get($merchantCriteriaTransfer);

        // Assert
        $this->assertCount(1, $merchantCollectionTransfer->getMerchants());
        $this->assertSame(2, $merchantCollectionTransfer->getPaginationOrFail()->getNbResults());
        $this->assertSame(2, $merchantCollectionTransfer->getPaginationOrFail()->getLastPage());
    }

    public function testGetReturnsOnlyMerchantsWhoseStatusIsInTheRequestedStatuses(): void
    {
        // Arrange
        $this->tester->truncateMerchantRelations();
        $approvedMerchantTransfer = $this->tester->haveMerchant([MerchantTransfer::STATUS => MerchantConfig::STATUS_APPROVED]);
        $this->tester->haveMerchant([MerchantTransfer::STATUS => MerchantConfig::STATUS_DENIED]);

        $merchantCriteriaTransfer = (new MerchantCriteriaTransfer())
            ->addStatus(MerchantConfig::STATUS_APPROVED);

        // Act
        $merchantCollectionTransfer = $this->tester->getFacade()->get($merchantCriteriaTransfer);

        // Assert
        $this->assertCount(1, $merchantCollectionTransfer->getMerchants());
        $this->assertSame(
            $approvedMerchantTransfer->getIdMerchant(),
            $merchantCollectionTransfer->getMerchants()->offsetGet(0)->getIdMerchant(),
        );
    }

    public function testGetReturnsEachMerchantOnceWhenItIsAssignedToSeveralOfTheRequestedStores(): void
    {
        // Arrange
        $this->tester->truncateMerchantRelations();
        $firstStoreTransfer = $this->tester->haveStore([StoreTransfer::NAME => static::STORE_NAME_FIRST]);
        $secondStoreTransfer = $this->tester->haveStore([StoreTransfer::NAME => static::STORE_NAME_SECOND]);
        $this->tester->haveMerchant([
            MerchantTransfer::STORE_RELATION => [
                StoreRelationTransfer::ID_STORES => [
                    $firstStoreTransfer->getIdStoreOrFail(),
                    $secondStoreTransfer->getIdStoreOrFail(),
                ],
            ],
        ]);

        $merchantCriteriaTransfer = (new MerchantCriteriaTransfer())
            ->addStoreName(static::STORE_NAME_FIRST)
            ->addStoreName(static::STORE_NAME_SECOND);

        // Act
        $merchantCollectionTransfer = $this->tester->getFacade()->get($merchantCriteriaTransfer);

        // Assert
        $this->assertCount(1, $merchantCollectionTransfer->getMerchants());
    }

    public function testGetMatchesTheSearchTermAgainstTheMerchantNameCaseInsensitively(): void
    {
        // Arrange
        $this->tester->truncateMerchantRelations();
        $merchantTransfer = $this->tester->haveMerchant([MerchantTransfer::NAME => static::SEARCHABLE_MERCHANT_NAME]);
        $this->tester->haveMerchant([MerchantTransfer::NAME => static::NON_MATCHING_MERCHANT_NAME]);

        $merchantCriteriaTransfer = (new MerchantCriteriaTransfer())
            ->setSearchTerm(static::SEARCH_TERM_IN_DIFFERENT_CASE);

        // Act
        $merchantCollectionTransfer = $this->tester->getFacade()->get($merchantCriteriaTransfer);

        // Assert
        $this->assertCount(1, $merchantCollectionTransfer->getMerchants());
        $this->assertSame(
            $merchantTransfer->getIdMerchant(),
            $merchantCollectionTransfer->getMerchants()->offsetGet(0)->getIdMerchant(),
        );
    }

    public function testReturnsMerchantsPaginatedByLimitAndOffset(): void
    {
        // Arrange
        $this->tester->truncateMerchantRelations();
        $this->tester->haveMerchant();
        $merchantTransfer = $this->tester->haveMerchant();

        $paginationTransfer = (new PaginationTransfer())
            ->setLimit(1)
            ->setOffset(1);

        $filterTransfer = new FilterTransfer();
        $filterTransfer->setOrderBy(SpyMerchantTableMap::COL_ID_MERCHANT);
        $filterTransfer->setOrderDirection('ASC');

        $merchantCriteriaTransfer = (new MerchantCriteriaTransfer())
            ->setPagination($paginationTransfer)
            ->setFilter($filterTransfer);

        // Act
        $merchantCollectionTransfer = $this->tester->getFacade()->get($merchantCriteriaTransfer);

        // Assert
        $this->assertCount(1, $merchantCollectionTransfer->getMerchants());
        $this->assertSame($merchantTransfer->getIdMerchant(), $merchantCollectionTransfer->getMerchants()->offsetGet(0)->getIdMerchant());
    }
}
