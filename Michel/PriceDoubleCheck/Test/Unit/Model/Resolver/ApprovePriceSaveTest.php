<?php

namespace Michel\PriceDoubleCheck\Test\Unit\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\Product;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;

use Michel\PriceDoubleCheck\Model\PriceApprove;
use Michel\PriceDoubleCheck\Model\Resolver\ApprovePriceSave;
use PHPUnit\Framework\TestCase;

class ApprovePriceSaveTest extends TestCase
{
    protected $approvePriceModelMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->approvePriceModelMock = $this->getMockBuilder(PriceApprove::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->productFactoryMock = $this->getMockBuilder(ProductFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['create'])
            ->addMethods(['loadByAttribute'])
            ->getMock();

        $this->productMock = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getData', 'setPrice', 'save'])
            ->getMock();

        $this->pricingHelperMock = $this->getMockBuilder(PricingHelper::class)
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
     */
    public function setApprovePriceMock()
    {
        $this->approvePriceModelMock->expects($this->once())->method('getId')->willReturn(1234);
        $this->approvePriceModelMock->expects($this->once())->method('getName')->willReturn("Name Product");
        $this->approvePriceModelMock->expects($this->once())->method('getSku')->willReturn("SKU");
        $this->approvePriceModelMock->expects($this->once())->method('getDatetime')->willReturn("2023-01-14 20:33:36");
        $this->approvePriceModelMock->expects($this->once())->method('getAttributeCode')->willReturn('price');
        $this->approvePriceModelMock->expects($this->once())->method('getPriceBefore')->willReturn('30.50');
        $this->approvePriceModelMock->expects($this->once())->method('getPriceAfterApprove')->willReturn('17.50');
        $this->approvePriceModelMock->expects($this->any())->method('load')->willReturnSelf();
        $this->approvePriceModelMock->method('hasData')->willReturnOnConsecutiveCalls(false, true);
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testResolve()
    {
        $this->pricingHelperMock->method('currency')->willReturnOnConsecutiveCalls((float)'20.50', (float)'27.50', (float)'27.50');

        $this->productMock->expects($this->any())->method('getData')->with('price')->willReturn((float)"20.50");
        $this->productFactoryMock->expects($this->any())->method('create')->willReturnSelf();
        $this->productFactoryMock->expects($this->any())->method('loadByAttribute')->willReturn($this->productMock);

        $this->setApprovePriceMock();
        $approvePriceResolver = new ApprovePriceSave($this->approvePriceModelMock, $this->productFactoryMock, $this->pricingHelperMock);

        try {
            $approvePriceResolver->resolve($this->fieldMock, null, $this->resolveInfoMock, [], []);
        } catch (\Exception $e) {
            $this->assertEquals('Required parameter "SKU" is missing', $e->getMessage());
        }

        try {
            $arg['input']['sku'] = "SKU";
            $approvePriceResolver->resolve($this->fieldMock, null, $this->resolveInfoMock, [], $arg);
        } catch (\Exception $e) {
            $this->assertEquals('Required parameter "PRICE" is missing', $e->getMessage());
        }

        try {
            $arg['input']['price'] = "20.50";
            $approvePriceResolver->resolve($this->fieldMock, null, $this->resolveInfoMock, [], $arg);
        } catch (\Exception $e) {
            $this->assertEquals('PRICE value is already in use.', $e->getMessage());
        }

        try {
            $arg['input']['price'] = "27.50";
            $approvePriceResolver->resolve($this->fieldMock, null, $this->resolveInfoMock, [], $arg);
        } catch (\Exception $e) {
            $this->assertEquals('It was not possible to add. An error has occurred.', $e->getMessage());
        }

        $responseSuccess = $approvePriceResolver->resolve($this->fieldMock, null, $this->resolveInfoMock, [], $arg);
        $this->assertEquals(1234, $responseSuccess['item']['id']);
    }
}
