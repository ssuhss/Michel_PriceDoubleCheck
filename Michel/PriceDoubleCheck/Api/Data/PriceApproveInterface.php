<?php

declare(strict_types=1);

namespace Michel\PriceDoubleCheck\Api\Data;

use Michel\PriceDoubleCheck\Model\PriceApprove;

interface PriceApproveInterface
{
    const ID                    = 'id';
    const NAME                  = 'name';
    const SKU                   = 'sku';
    const DATETIME              = 'datetime';
    const ATTRIBUTE             = 'attribute_code';
    const PRICE_BEFORE          = 'price_before';
    const PRICE_AFTER_APPROVE   = 'price_after_approve';
    const USER_ID   = 'admin_user_id';

    /**
     * @return int
     */
    public function getId(): int;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function getSku(): string;

    /**
     * @return string
     */
    public function getDatetime(): string;

    /**
     * @return string
     */
    public function getAttributeCode(): string;

    /**
     * @return string
     */
    public function getPriceBefore(): string;

    /**
     * @return string
     */
    public function getPriceAfterApprove(): string;

    /**
     * @return int
     */
    public function getUserId(): int;

    /**
     * @param PriceApprove $priceApproveModel
     * @return void
     */
    public function approve(PriceApprove $priceApproveModel): void;

    /**
     * @param PriceApprove $priceApproveModel
     * @return void
     */
    public function reprove(PriceApprove $priceApproveModel): void;

}
