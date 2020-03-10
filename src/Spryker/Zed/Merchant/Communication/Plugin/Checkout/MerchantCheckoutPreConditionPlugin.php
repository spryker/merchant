<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Merchant\Communication\Plugin\Checkout;

use ArrayObject;
use Generated\Shared\Transfer\CheckoutErrorTransfer;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\MerchantCriteriaFilterTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Zed\CheckoutExtension\Dependency\Plugin\CheckoutPreConditionPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * @method \Spryker\Zed\Merchant\Business\MerchantFacadeInterface getFacade()
 * @method \Spryker\Zed\Merchant\MerchantConfig getConfig()
 */
class MerchantCheckoutPreConditionPlugin extends AbstractPlugin implements CheckoutPreConditionPluginInterface
{
    protected const GLOSSARY_KEY_INACTIVE_MERCHANT_PROFILE = 'merchant_profile.message.inactive';
    protected const GLOSSARY_KEY_REMOVED_MERCHANT_PROFILE = 'merchant_profile.message.removed';

    protected const GLOSSARY_PARAM_SKU = '%sku%';
    protected const GLOSSARY_PARAM_MERCHANT_NAME = '%merchant_name%';

    /**
     * {@inheritDoc}
     * - Returns `false` response if at least one quote item transfer has items with inactive merchant.
     * - Sets error messages to checkout response, in case if items contains inactive merchants items.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return bool
     */
    public function checkCondition(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponseTransfer): bool
    {
        $checkoutErrorTransfers = [];

        foreach ($quoteTransfer->getItems() as $itemTransfer) {
            if (!$itemTransfer->getMerchantReference()) {
                continue;
            }

            $merchantTransfer = $this->getFacade()->findOne(
                (new MerchantCriteriaFilterTransfer())
                    ->setMerchantReference($itemTransfer->getMerchantReference())
            );

            if (!$merchantTransfer) {
                $checkoutErrorTransfers[] = (new CheckoutErrorTransfer())
                    ->setMessage(static::GLOSSARY_KEY_REMOVED_MERCHANT_PROFILE)
                    ->setParameters([static::GLOSSARY_PARAM_SKU => $itemTransfer->getSku()]);
            }

            if (!$merchantTransfer->getIsActive()) {
                $checkoutErrorTransfers[] = (new CheckoutErrorTransfer())
                    ->setMessage(static::GLOSSARY_KEY_INACTIVE_MERCHANT_PROFILE)
                    ->setParameters([
                        static::GLOSSARY_PARAM_SKU => $itemTransfer->getSku(),
                        static::GLOSSARY_PARAM_MERCHANT_NAME => $merchantTransfer->getName(),
                    ]);
            }
        }

        $checkoutResponseTransfer
            ->setIsSuccess(!$checkoutErrorTransfers)
            ->setErrors(new ArrayObject($checkoutErrorTransfers));

        return !$checkoutErrorTransfers;
    }
}
