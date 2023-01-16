<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Michel\PriceDoubleCheck\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Michel\PriceDoubleCheck\Api\Data\PriceApproveInterface;
use Michel\PriceDoubleCheck\Model\PriceApprove as PriceApproveModel;

class ApprovePriceList implements ResolverInterface
{
    protected PriceApproveModel $priceApproveModel;

    public function __construct(
        PriceApproveModel $priceApproveModel
    ) {
        $this->priceApproveModel = $priceApproveModel;
    }

    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $allPrices = $this->priceApproveModel->loadAll();
        if (empty($allPrices)) {
            throw new GraphQlNoSuchEntityException(__('No values found.'));
        }

        $items = [];
        foreach ($allPrices as $key => $price) {
            $items['items'][$key] = [
                'id' => (int)$price[PriceApproveInterface::ID],
                'name' => $price[PriceApproveInterface::NAME],
                'sku' => $price[PriceApproveInterface::SKU],
                'datetime' => $price[PriceApproveInterface::DATETIME],
                'attribute_code' => $price[PriceApproveInterface::ATTRIBUTE],
                'price_before' => $price[PriceApproveInterface::PRICE_BEFORE],
                'price_after_approve' => $price[PriceApproveInterface::PRICE_AFTER_APPROVE],
            ];
        }

        return $items;
    }
}
