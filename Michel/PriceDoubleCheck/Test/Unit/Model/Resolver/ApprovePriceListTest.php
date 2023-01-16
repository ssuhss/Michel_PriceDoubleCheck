<?php

namespace Michel\PriceDoubleCheck\Test\Unit\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

use Michel\PriceDoubleCheck\Api\Data\PriceApproveInterface;
use Michel\PriceDoubleCheck\Model\PriceApprove;
use Michel\PriceDoubleCheck\Model\Resolver\ApprovePriceList;
use PHPUnit\Framework\TestCase;

class ApprovePriceListTest extends TestCase
{
    protected $approvePriceModelMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->approvePriceModelMock = $this->getMockBuilder(PriceApprove::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->fieldMock = $this->getMockBuilder(Field::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->resolveInfoMock = $this->getMockBuilder(ResolveInfo::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testResolve()
    {
        $array[] = [
            PriceApproveInterface::ID => 256,
            PriceApproveInterface::NAME => "Nome Product 1",
            PriceApproveInterface::SKU => "SKUPROD1",
            PriceApproveInterface::DATETIME => "2023-01-14 20:33:27",
            PriceApproveInterface::ATTRIBUTE => "price",
            PriceApproveInterface::PRICE_BEFORE => "50.20",
            PriceApproveInterface::PRICE_AFTER_APPROVE => "25.60"
        ];

        $this->approvePriceModelMock->method('loadAll')->willReturnOnConsecutiveCalls([], $array);
        $approvePriceResolver = new ApprovePriceList($this->approvePriceModelMock);

        try {
            $approvePriceResolver->resolve($this->fieldMock, null, $this->resolveInfoMock, [], []);
        } catch (\Exception $e) {
            $this->assertEquals('No values found.', $e->getMessage());
        }

        $items = $approvePriceResolver->resolve($this->fieldMock, null, $this->resolveInfoMock, [], []);
        $this->assertEquals($array, $items['items']);
    }
}
