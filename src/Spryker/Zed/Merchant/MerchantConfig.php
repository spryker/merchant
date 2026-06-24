<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Merchant;

use Generated\Shared\Transfer\MerchantCreatedTransfer;
use Generated\Shared\Transfer\MerchantExportedTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Generated\Shared\Transfer\MerchantUpdatedTransfer;
use Generated\Shared\Transfer\StoreRelationTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use Spryker\Shared\Merchant\MerchantConstants;
use Spryker\Zed\Kernel\AbstractBundleConfig;

class MerchantConfig extends AbstractBundleConfig
{
    protected const string CHECKOUT_ERROR_TYPE = 'MerchantUnavailable';

    /**
     * @api
     *
     * @var string
     */
    public const STATUS_WAITING_FOR_APPROVAL = 'waiting-for-approval';

    /**
     * @api
     *
     * @var string
     */
    public const STATUS_APPROVED = 'approved';

    /**
     * @api
     *
     * @var string
     */
    public const STATUS_DENIED = 'denied';

    /**
     * Specification:
     * - Returns the error type identifier used in CheckoutErrorTransfer for errors produced by this module.
     *
     * @api
     */
    public function getCheckoutErrorType(): string
    {
        return static::CHECKOUT_ERROR_TYPE;
    }

    /**
     * @api
     *
     * @return string
     */
    public function getDefaultMerchantStatus(): string
    {
        return static::STATUS_WAITING_FOR_APPROVAL;
    }

    /**
     * @api
     *
     * @return array<string, array<string>>
     */
    public function getStatusTree(): array
    {
        return [
            static::STATUS_WAITING_FOR_APPROVAL => [
                static::STATUS_APPROVED,
                static::STATUS_DENIED,
            ],
            static::STATUS_APPROVED => [
                static::STATUS_DENIED,
            ],
            static::STATUS_DENIED => [
                static::STATUS_APPROVED,
            ],
        ];
    }

    /**
     * Specification:
     * - Used to decide if internal merchant events have to be sent to the Message Broker
     *
     * @api
     *
     * @return bool
     */
    public function isPublishingToMessageBrokerEnabled(): bool
    {
        return (bool)$this->get(MerchantConstants::PUBLISHING_TO_MESSAGE_BROKER_ENABLED, true);
    }

    /**
     * @api
     *
     * @deprecated Will be removed without replacement.
     *
     * @return array<string>
     */
    public function getMerchantEventsAllowedForPublish(): array
    {
        return [
            MerchantExportedTransfer::class,
            MerchantCreatedTransfer::class,
            MerchantUpdatedTransfer::class,
        ];
    }

    /**
     * @api
     *
     * @deprecated Will be removed without replacement.
     *
     * @return array
     */
    public function getMerchantFieldsForMerchantEventMessage(): array
    {
        return [
            MerchantTransfer::MERCHANT_REFERENCE => MerchantTransfer::MERCHANT_REFERENCE,
            MerchantTransfer::NAME => MerchantTransfer::NAME,
            MerchantTransfer::EMAIL => MerchantTransfer::EMAIL,
        ];
    }

    /**
     * @api
     *
     * @deprecated Will be removed without replacement.
     *
     * @return array
     */
    public function getMerchantStoreRelationFieldsForMerchantEventMessage(): array
    {
        return [
            StoreRelationTransfer::STORES => [
                StoreTransfer::STORE_REFERENCE => StoreTransfer::STORE_REFERENCE,
            ],
        ];
    }
}
