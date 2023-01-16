<?php

namespace Michel\PriceDoubleCheck\Model\ResourceModel\PriceApprove;

use Michel\PriceDoubleCheck\Api\Data\PriceApproveInterface;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'id';

    protected function _construct()
    {
        $this->_init(
            \Michel\PriceDoubleCheck\Model\PriceApprove::class,
            \Michel\PriceDoubleCheck\Model\ResourceModel\PriceApprove::class
        );
        $this->_setIdFieldName($this->_idFieldName);
    }

}
