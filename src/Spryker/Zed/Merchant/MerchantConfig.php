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

    protected const int MERCHANT_URL_MAX_LENGTH = 255;

    protected const string MERCHANT_URL_PREFIX = 'merchant';

    /**
     * @api
     */
    public const string STATUS_WAITING_FOR_APPROVAL = 'waiting-for-approval';

    /**
     * @api
     */
    public const string STATUS_APPROVED = 'approved';

    /**
     * @api
     */
    public const string STATUS_DENIED = 'denied';

    /**
     * @api
     *
     * @var array<string>
     */
    public const array SUPPORTED_STATUSES = [
        self::STATUS_WAITING_FOR_APPROVAL,
        self::STATUS_APPROVED,
        self::STATUS_DENIED,
    ];

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
     * Specification:
     * - Returns the maximum number of characters a merchant URL may have.
     *
     * @uses \Orm\Zed\Url\Persistence\Map\SpyUrlTableMap - `spy_url.url` is a `VARCHAR(255)`.
     *
     * @api
     *
     * @return int
     */
    public function getMerchantUrlMaxLength(): int
    {
        return static::MERCHANT_URL_MAX_LENGTH;
    }

    /**
     * Specification:
     * - Returns the URL path segment used to build a merchant's localized URL, e.g. `/de/merchant/`.
     *
     * @api
     *
     * @return string
     */
    public function getMerchantUrlPrefix(): string
    {
        return static::MERCHANT_URL_PREFIX;
    }

    /**
     * @api
     *
     * @return array<string>
     */
    public function getSupportedMerchantStatuses(): array
    {
        return static::SUPPORTED_STATUSES;
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
     * @return array<string, string>
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
     * @return array<string, array<string, string>>
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
