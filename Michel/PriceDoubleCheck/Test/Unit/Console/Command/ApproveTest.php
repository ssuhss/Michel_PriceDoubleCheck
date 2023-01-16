<?php

namespace Michel\PriceDoubleCheck\Test\Unit\Console\Command;

use Michel\PriceDoubleCheck\Console\Command\Approve;
use Michel\PriceDoubleCheck\Model\PriceApprove;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class ApproveTest extends TestCase
{
    /**
     * @var MockObject
     */
    protected $approvePriceModelMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->approvePriceModelMock = $this->getMockBuilder(PriceApprove::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->approvePriceModelMock->expects($this->exactly(2))->method('load')->willReturnSelf();
        $this->approvePriceModelMock->expects($this->once())->method('hasData')->willReturn(true);
        $this->approvePriceModelMock->expects($this->once())->method('approve');


        $this->command = new Approve($this->approvePriceModelMock);
    }

    /**
     * @return void
     */
    public function testExecute()
    {
        $commandTester = new CommandTester($this->command);
        $commandTester->execute(
            [
                '--sku' => 'ABCDE123',
                '--id' => '123'
            ]
        );
        $this->assertEquals('Price approved' . PHP_EOL, $commandTester->getDisplay());

        $commandTester->execute(
            [
                '--sku' => ''
            ]
        );
        $this->assertEquals('ID or SKU is required.' . PHP_EOL, $commandTester->getDisplay());
    }
}
