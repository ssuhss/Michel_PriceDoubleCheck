<?php

namespace Michel\PriceDoubleCheck\Test\Unit\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

use Michel\PriceDoubleCheck\Model\PriceApprove;
use Michel\PriceDoubleCheck\Model\Resolver\ApprovePrice;
use PHPUnit\Framework\TestCase;

class ApprovePriceTest extends TestCase
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
        $this->approvePriceModelMock->expects($this->any())->method('load')->willReturnSelf();
        $this->approvePriceModelMock->method('hasData')->willReturnOnConsecutiveCalls(false, true);

        $approvePriceResolver = new ApprovePrice($this->approvePriceModelMock);

        try {
            $approvePriceResolver->resolve($this->fieldMock, null, $this->resolveInfoMock, [], []);
        } catch (\Exception $e) {
            $this->assertEquals('Required parameter "id" is missing', $e->getMessage());
        }

        $arg['input']['id'] = 200;
        $result1 = $approvePriceResolver->resolve($this->fieldMock, null, $this->resolveInfoMock, [], $arg);
        $this->assertEquals('ID: ' . $arg['input']['id'] . ' not found to approve', $result1['message']->getText());

        $arg['input']['id'] = 215;
        $result2 = $approvePriceResolver->resolve($this->fieldMock, null, $this->resolveInfoMock, [], $arg);
        $this->assertEquals('Success', $result2['message']->getText());
    }
}
