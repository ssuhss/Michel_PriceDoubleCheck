<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Michel\PriceDoubleCheck\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Michel\PriceDoubleCheck\Model\PriceApprove as PriceApproveModel;

class ApprovePrice implements ResolverInterface
{
    protected PriceApproveModel $priceApproveModel;

    public function __construct(
        PriceApproveModel $priceApproveModel
    ) {
        $this->priceApproveModel = $priceApproveModel;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (empty($args['input']['id'])) {
            throw new GraphQlInputException(__('Required parameter "id" is missing'));
        }
        $priceApprove = $this->priceApproveModel->load($args['input']['id']);
        if (!$priceApprove->hasData('id')) {
            return ['message' => __('ID: '.$args['input']['id'].' not found to approve')];
        }
        $this->priceApproveModel->approve($priceApprove);
        return ['message' => __('Success')];
    }
}
