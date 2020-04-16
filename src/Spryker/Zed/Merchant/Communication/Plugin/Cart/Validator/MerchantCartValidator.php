<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Merchant\Communication\Plugin\Cart\Validator;

use ArrayObject;
use Generated\Shared\Transfer\CartChangeTransfer;
use Generated\Shared\Transfer\CartPreCheckResponseTransfer;
use Generated\Shared\Transfer\MerchantCriteriaTransfer;
use Generated\Shared\Transfer\MessageTransfer;
use Spryker\Zed\Merchant\Business\MerchantFacadeInterface;
use Spryker\Zed\Merchant\Dependency\Facade\MerchantToStoreFacadeInterface;

class MerchantCartValidator implements MerchantCartValidatorInterface
{
    protected const MESSAGE_TYPE_ERROR = 'error';
    protected const GLOSSARY_KEY_REMOVED_MERCHANT = 'merchant.message.removed';
    protected const GLOSSARY_PARAM_SKU = '%sku%';

    /**
     * @var \Spryker\Zed\Merchant\Business\MerchantFacadeInterface
     */
    protected $merchantFacade;

    /**
     * @var \Spryker\Zed\Merchant\Dependency\Facade\MerchantToStoreFacadeInterface
     */
    protected $storeFacade;

    /**
     * @param \Spryker\Zed\Merchant\Business\MerchantFacadeInterface $merchantFacade
     * @param \Spryker\Zed\Merchant\Dependency\Facade\MerchantToStoreFacadeInterface $storeFacade
     */
    public function __construct(
        MerchantFacadeInterface $merchantFacade,
        MerchantToStoreFacadeInterface $storeFacade
    ) {
        $this->merchantFacade = $merchantFacade;
        $this->storeFacade = $storeFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\CartChangeTransfer $cartChangeTransfer
     *
     * @return \Generated\Shared\Transfer\CartPreCheckResponseTransfer
     */
    public function check(CartChangeTransfer $cartChangeTransfer): CartPreCheckResponseTransfer
    {
        $messageTransfers = [];
        $merchantTransfers = $this->getMerchantTransfersGroupedByMerchantReference($cartChangeTransfer);

        foreach ($cartChangeTransfer->getItems() as $itemTransfer) {
            if (!$itemTransfer->getMerchantReference()) {
                continue;
            }

            if (!isset($merchantTransfers[$itemTransfer->getMerchantReference()])) {
                $messageTransfers[] = (new MessageTransfer())
                    ->setType(static::MESSAGE_TYPE_ERROR)
                    ->setValue(static::GLOSSARY_KEY_REMOVED_MERCHANT)
                    ->setParameters([static::GLOSSARY_PARAM_SKU => $itemTransfer->getSku()]);

                continue;
            }
        }

        return (new CartPreCheckResponseTransfer())
            ->setMessages(new ArrayObject($messageTransfers))
            ->setIsSuccess(!$messageTransfers);
    }

    /**
     * @param \Generated\Shared\Transfer\CartChangeTransfer $cartChangeTransfer
     *
     * @return array
     */
    protected function getMerchantTransfersGroupedByMerchantReference(CartChangeTransfer $cartChangeTransfer)
    {
        $merchantReferences = [];
        $merchantTransfers = [];

        foreach ($cartChangeTransfer->getItems() as $itemTransfer) {
            if (!$itemTransfer->getMerchantReference()) {
                continue;
            }
            $merchantReferences[] = $itemTransfer->getMerchantReference();
        }

        if (!$merchantReferences) {
            return $merchantTransfers;
        }

        $merchantReferences = array_unique($merchantReferences);
        $merchantCollectionTransfer = $this->merchantFacade->get(
            (new MerchantCriteriaTransfer())
                ->setMerchantReferences($merchantReferences)
                ->setIsActive(true)
                ->setStore($this->storeFacade->getCurrentStore())
        );
        foreach ($merchantCollectionTransfer->getMerchants() as $merchantTransfer) {
            $merchantTransfers[$merchantTransfer->getMerchantReference()] = $merchantTransfer;
        }

        return $merchantTransfers;
    }
}
