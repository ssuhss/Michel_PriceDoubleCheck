<?php

namespace Michel\PriceDoubleCheck\Test\Unit\Model\ResourceModel;

use Michel\PriceDoubleCheck\Api\Data\PriceApproveInterface;
use Michel\PriceDoubleCheck\Model\ResourceModel\PriceApprove;
use Magento\Framework\DB\Adapter\AdapterInterface;
use PHPUnit\Framework\TestCase;

class PriceApproveTest extends TestCase
{
    protected $connection;
    protected $priceApproveMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection = $this->getMockBuilder(AdapterInterface::class)
            ->addMethods(['from'])
            ->getMockForAbstractClass();
        $this->connection->expects($this->any())->method('select')->willReturnSelf();
        $this->connection->expects($this->any())->method('from')->willReturnSelf();
        $this->connection->expects($this->any())->method('insert')->willReturnSelf();
        $this->connection->expects($this->any())->method('update')->willReturnSelf();
        $this->connection->expects($this->any())->method('quoteInto')->willReturn('WHERE id = 1');
        $this->connection->expects($this->any())->method('fetchAll')->willReturn([$this->getData()]);

        $this->priceApproveMock = $this->getMockBuilder(PriceApprove::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getMainTable', 'getConnection'])
            ->getMock();
        $this->priceApproveMock->expects($this->any())->method('getMainTable')->willReturn('price_approve');
        $this->priceApproveMock->expects($this->any())->method('getConnection')->willReturn($this->connection);
    }

    /**
     * @return void
     */
    public function testFetchAll()
    {
        $this->assertIsArray($this->priceApproveMock->fetchAll());
    }

    /**
     * @return void
     */
    public function testInsert()
    {
        $this->assertInstanceOf(AdapterInterface::class, $this->priceApproveMock->insert($this->getData()));
    }

    /**
     * @return void
     */
    public function testUpdate()
    {
        $this->assertInstanceOf(AdapterInterface::class, $this->priceApproveMock->update($this->getData()));
    }

    /**
     * @return array
     */
    public function getData()
    {
        $data[PriceApproveInterface::ID] = 1234;
        $data[PriceApproveInterface::NAME] = 'Name Product Mock';
        $data[PriceApproveInterface::SKU] = 'SKUPRODMOCK123';
        $data[PriceApproveInterface::DATETIME] = "2023-01-14 20:33:36";
        $data[PriceApproveInterface::ATTRIBUTE] = 'price';
        $data[PriceApproveInterface::PRICE_BEFORE] = '150.25';
        $data[PriceApproveInterface::PRICE_AFTER_APPROVE] = '170.50';
        $data[PriceApproveInterface::USER_ID] = '432';

        return $data;
    }
}
