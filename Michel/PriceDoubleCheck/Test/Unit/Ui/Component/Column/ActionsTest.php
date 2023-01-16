<?php
namespace Michel\PriceDoubleCheck\Test\Unit\Ui\Component\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Michel\PriceDoubleCheck\Ui\Component\Column\Actions;
use PHPUnit\Framework\TestCase;

class ActionsTest extends TestCase
{
    protected ContextInterface $contextMock;
    protected UiComponentFactory $uiComponentFactory;
    protected UrlInterface $urlBuilderMock;
    protected Actions $actionsGrid;

    protected function setUp(): void
    {
        parent::setUp();

        $this->urlBuilderMock = $this->getMockBuilder(UrlInterface::class)->getMockForAbstractClass();
        $this->urlBuilderMock->expects($this->once())->method('getUrl')->with(Actions::URL_PATH_APPROVE)->willReturn('www.phpunit.michel.com.br');
        $this->contextMock = $this->getMockBuilder(ContextInterface::class)->getMockForAbstractClass();

        $this->uiComponentFactory = $this->getMockBuilder(UiComponentFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->actionsGrid = new Actions(
            $this->contextMock,
            $this->uiComponentFactory,
            $this->urlBuilderMock
        );
    }

    /**
     * @return void
     */
    public function testPrepareDataSource()
    {
        $dataSourceMock['data']['items'] = [
            ['id' => 79]
        ];

        $this->actionsGrid->setData('name', 'Michel');
        $dataSource = $this->actionsGrid->prepareDataSource($dataSourceMock);
        $this->assertEquals($dataSourceMock['data']['items'][0]['id'], $dataSource['data']['items'][0]['id']);
    }
}
