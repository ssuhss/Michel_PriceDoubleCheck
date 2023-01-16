<?php

namespace Michel\PriceDoubleCheck\Model\ResourceModel;

use Michel\PriceDoubleCheck\Api\Data\PriceApproveInterface;

class PriceApprove extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const PRICE_APPROVE_TABLE = 'price_approve';

    protected $_idFieldName = 'id';

    protected function _construct()
    {
        $this->_init(self::PRICE_APPROVE_TABLE, 'id');
    }

    public function fetchAll()
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from($this->getMainTable());

        return $connection->fetchAll($select);
    }

    public function insert($data)
    {
        return $this->getConnection()->insert($this->getMainTable(), $data);
    }

    public function update($data)
    {
        $where = $this->getConnection()->quoteInto('id=?', $data[PriceApproveInterface::ID]);
        return $this->getConnection()->update($this->getMainTable(), $data, $where);
    }
}
