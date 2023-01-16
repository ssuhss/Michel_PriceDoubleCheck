<?php

namespace Michel\PriceDoubleCheck\Test\Unit\Observer;

use Magento\Catalog\Model\Product;
use Magento\Framework\Event\Observer;
use Michel\PriceDoubleCheck\Model\PriceApprove;
use Michel\PriceDoubleCheck\Observer\DoubleCheckObserver;
use PHPUnit\Framework\TestCase;

class DoubleCheckObserverTest extends TestCase
{
    protected $approvePriceModelMock;
    protected $productMock;
    protected $eventObserver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productMock = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->approvePriceModelMock = $this->getMockBuilder(PriceApprove::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->eventObserver = $this->getMockBuilder(Observer::class)
            ->disableOriginalConstructor()
            ->addMethods(['getProduct'])
            ->getMock();
    }

    /**
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function testExecute()
    {
        $this->productMock->method('hasDataChanges')->willReturnOnConsecutiveCalls(false, true);
        $this->productMock->method('getData')->with('price')->willReturn(20);
        $this->productMock->method('getOrigData')->with('price')->willReturn(25);
        $this->productMock->method('setData')->willReturnSelf();

        $this->eventObserver->expects($this->any())->method('getProduct')->willReturn($this->productMock);

        $observer = new DoubleCheckObserver($this->approvePriceModelMock);
        $this->assertInstanceOf(\Michel\PriceDoubleCheck\Observer\DoubleCheckObserver::class, $observer->execute($this->eventObserver));
        $this->assertNull($observer->execute($this->eventObserver));
    }
}
