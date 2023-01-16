<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Michel\PriceDoubleCheck\Model\Resolver;

use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAlreadyExistsException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;
use Michel\PriceDoubleCheck\Model\PriceApprove as PriceApproveModel;

class ApprovePriceSave implements ResolverInterface
{
    protected PriceApproveModel $priceApproveModel;
    protected ProductFactory $productFactory;
    protected PricingHelper $pricingHelper;

    public function __construct(
        PriceApproveModel $priceApproveModel,
        ProductFactory    $productFactory,
        PricingHelper     $pricingHelper
    )
    {
        $this->productFactory = $productFactory;
        $this->priceApproveModel = $priceApproveModel;
        $this->pricingHelper = $pricingHelper;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (empty($args['input']['sku'])) {
            throw new GraphQlInputException(__('Required parameter "SKU" is missing'));
        }

        if (empty($args['input']['price'])) {
            throw new GraphQlInputException(__('Required parameter "PRICE" is missing'));
        }

        $formattedPrice = number_format($this->pricingHelper->currency($args['input']['price'], false, false), 2);

        $productFactory = $this->productFactory->create();
        $product = $productFactory->loadByAttribute('sku', $args['input']['sku']);
        if ($product->getData('price') == $formattedPrice) {
            throw new GraphQlAlreadyExistsException(__('PRICE value is already in use.'));
        }
        $product->setPrice($formattedPrice);
        $product->save();

        $priceApproveRow = $this->priceApproveModel->load($args['input']['sku'], 'sku');
        if (!$priceApproveRow->hasData('id')) {
            throw new GraphQlNoSuchEntityException(__('It was not possible to add. An error has occurred.'));
        }

        return [
            'item' => [
                'id' => $priceApproveRow->getId(),
                'name' => $priceApproveRow->getName(),
                'sku' => $priceApproveRow->getSku(),
                'datetime' => $priceApproveRow->getDatetime(),
                'attribute_code' => $priceApproveRow->getAttributeCode(),
                'price_before' => $priceApproveRow->getPriceBefore(),
                'price_after_approve' => $priceApproveRow->getPriceAfterApprove()
            ]
        ];
    }
}
