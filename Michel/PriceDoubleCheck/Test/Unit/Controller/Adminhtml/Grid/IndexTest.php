<?php

namespace Michel\PriceDoubleCheck\Test\Unit\Controller\Adminhtml\Grid;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Michel\PriceDoubleCheck\Controller\Adminhtml\Grid\Index;

use PHPUnit\Framework\TestCase;

class IndexTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->contextMock = $this->createMock(Context::class);

        $this->resultPageFactoryMock = $this->getMockBuilder(PageFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['create'])
            ->addMethods(['getConfig', 'getTitle', 'prepend'])
            ->getMock();

        $this->resultPageFactoryMock->expects($this->once())->method('create')->willReturnSelf();
        $this->resultPageFactoryMock->expects($this->once())->method('getConfig')->willReturnSelf();
        $this->resultPageFactoryMock->expects($this->once())->method('getTitle')->willReturnSelf();
        $this->resultPageFactoryMock->expects($this->any())->method('prepend')->willReturn('Grid');
    }

    /**
     * @return void
     */
    public function testExecute()
    {
        $index = new Index($this->contextMock, $this->resultPageFactoryMock);
        $resultIndex = $index->execute();

        $this->assertEquals($this->resultPageFactoryMock->prepend(), $resultIndex->prepend());
    }
}
