<?php

namespace Michel\PriceDoubleCheck\Test\Unit\Console\Command;

use Michel\PriceDoubleCheck\Api\Data\PriceApproveInterface;
use Michel\PriceDoubleCheck\Console\Command\ListApprovePrice;
use Michel\PriceDoubleCheck\Model\PriceApprove;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class ListApprovePriceTest extends TestCase
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

        $this->approvePriceModelMock->method('loadAll')->willReturnOnConsecutiveCalls(
            $this->getApprovePriceMockArray(),
            $this->throwException(new \Exception('Forced Exception'))
        );

        $this->command = new ListApprovePrice($this->approvePriceModelMock);
    }

    /**
     * @return array
     */
    public function getApprovePriceMockArray()
    {
        $item[0][PriceApproveInterface::ID] = 123;
        $item[0][PriceApproveInterface::NAME] = 'Item 1 Name';
        $item[0][PriceApproveInterface::SKU] = 'ITEM1SKU';
        $item[0][PriceApproveInterface::DATETIME] = '2023-01-14 20:33:36';
        $item[0][PriceApproveInterface::ATTRIBUTE] = 'price';
        $item[0][PriceApproveInterface::PRICE_BEFORE] = '30.00';
        $item[0][PriceApproveInterface::PRICE_AFTER_APPROVE] = '10.50';
        return $item;
    }

    /**
     * @return void
     */
    public function testExecute()
    {
        $commandTester = new CommandTester($this->command);
        $commandTester->execute([]);

        $this->assertEquals($this->getTextResult(), $commandTester->getDisplay());

        $commandTester->execute([]);
        $this->assertEquals('Forced Exception' . PHP_EOL, $commandTester->getDisplay());
    }

    /**
     * @return string
     */
    public function getTextResult()
    {
        $result = "+-----+-------------+----------+---------------------+----------+----------------+-------------+" . PHP_EOL;
        $result .= "| ID  | Nome        | SKU      | Data                | Atributo | Valor Anterior | Valor Atual |" . PHP_EOL;
        $result .= "+-----+-------------+----------+---------------------+----------+----------------+-------------+" . PHP_EOL;
        $result .= "| 123 | Item 1 Name | ITEM1SKU | 2023-01-14 20:33:36 | price    | 30.00          | 10.50       |" . PHP_EOL;
        $result .= "+-----+-------------+----------+---------------------+----------+----------------+-------------+" . PHP_EOL;
        return $result;
    }
}
