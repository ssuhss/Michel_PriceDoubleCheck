<?php

namespace Michel\PriceDoubleCheck\Test\Unit\Model;

use Magento\Backend\Model\Auth\Session;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\App\State;
use Magento\Framework\Data\Collection\AbstractDb;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use Michel\PriceDoubleCheck\Api\Data\PriceApproveInterface;
use Michel\PriceDoubleCheck\Helper\Email as EmailHelper;

use Michel\PriceDoubleCheck\Model\PriceApprove;
use PHPUnit\Framework\TestCase;

class PriceApproveTest extends TestCase
{
    protected $context;
    protected $registry;
    protected $productMock;
    protected $productFactoryMock;
    protected $storeManagerMock;
    protected $sessionMock;
    protected $emailHelper;
    protected $abstractResourceMock;
    protected $abstractDbMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->context = $this->getMockBuilder(Context::class)->disableOriginalConstructor()->getMock();
        $this->registry = $this->getMockBuilder(Registry::class)->disableOriginalConstructor()->onlyMethods(['registry'])->getMock();

        $this->productMock = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->productMock->expects($this->any())->method('hasData')->willReturn(true);
        $this->productMock->expects($this->any())->method('addAttributeUpdate')->willReturnSelf();

        $this->productFactoryMock = $this->getMockBuilder(ProductFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['create'])
            ->addMethods(['loadByAttribute'])
            ->getMock();
        $this->productFactoryMock->expects($this->any())->method('create')->willReturnSelf();
        $this->productFactoryMock->expects($this->any())->method('loadByAttribute')->willReturn($this->productMock);

        $this->storeManagerMock = $this->getMockBuilder(StoreManagerInterface::class)
            ->addMethods(['getId'])
            ->getMockForAbstractClass();
        $this->storeManagerMock->expects($this->any())->method('getStore')->willReturnSelf();
        $this->storeManagerMock->expects($this->any())->method('getId')->willReturn(1);

        $this->sessionMock = $this->getMockBuilder(Session::class)
            ->addMethods(['getUser', 'getAclRole', 'hasUser', 'getId'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->emailHelper = $this->getMockBuilder(EmailHelper::class)
            ->onlyMethods(['setProduct', 'sendEmail'])
            ->addMethods(['getUser', 'getAclRole', 'hasUser'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->emailHelper->expects($this->any())->method('setProduct')->willReturnSelf();
        $this->emailHelper->expects($this->any())->method('sendEmail')->willReturnSelf();

        $this->abstractResourceMock = $this->getMockBuilder(AbstractResource::class)
            ->addMethods(['updateAttributes', 'getIdFieldName', 'fetchAll'])
            ->onlyMethods(['getConnection'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->abstractResourceMock->expects($this->any())->method('fetchAll')->willReturn($this->getFormattedData());

        $this->abstractDbMock = $this->getMockBuilder(AbstractDb::class)
            ->disableOriginalConstructor()
            ->addMethods(['getCategoryCollection'])
            ->getMockForAbstractClass();
    }

    /**
     * @return PriceApprove
     */
    public function getPriceApproveModel()
    {
        $priceApproveModel = new PriceApprove(
            $this->context,
            $this->registry,
            $this->productFactoryMock,
            $this->storeManagerMock,
            $this->sessionMock,
            $this->emailHelper,
            $this->abstractResourceMock,
            $this->abstractDbMock,
            []
        );

        $priceApproveModel->setData(PriceApproveInterface::ID, 123);
        $priceApproveModel->setData(PriceApproveInterface::NAME, 'Name Product Mock');
        $priceApproveModel->setData(PriceApproveInterface::SKU, 'SKUPRODMOCK123');
        $priceApproveModel->setData(PriceApproveInterface::DATETIME, "2023-01-14 20:33:36");
        $priceApproveModel->setData(PriceApproveInterface::ATTRIBUTE, 'price');
        $priceApproveModel->setData(PriceApproveInterface::PRICE_AFTER_APPROVE, '170.50');
        $priceApproveModel->setData(PriceApproveInterface::PRICE_BEFORE, '150.25');
        $priceApproveModel->setData(PriceApproveInterface::USER_ID, 432);

        return $priceApproveModel;
    }

    public function testGets()
    {
        $priceApproveModel = $this->getPriceApproveModel();
        $this->assertEquals(123, $priceApproveModel->getId());
        $this->assertEquals('Name Product Mock', $priceApproveModel->getName());
        $this->assertEquals('SKUPRODMOCK123', $priceApproveModel->getSku());
        $this->assertEquals("2023-01-14 20:33:36", $priceApproveModel->getDatetime());
        $this->assertEquals('price', $priceApproveModel->getAttributeCode());
        $this->assertEquals('170.50', $priceApproveModel->getPriceAfterApprove());
        $this->assertEquals('150.25', $priceApproveModel->getPriceBefore());
        $this->assertNotEquals(123, $priceApproveModel->getUserId());
    }

    /**
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function testApprove()
    {
        $priceApproveModelMock = $this->getMockBuilder(PriceApprove::class)
            ->disableOriginalConstructor()
            ->getMock();
        $priceApproveModelMock->expects($this->once())->method('delete')->willReturnSelf();

        $priceApproveModel = $this->getPriceApproveModel();
        $this->assertNull($priceApproveModel->approve($priceApproveModelMock));
    }

    /**
     * @return void
     * @throws LocalizedException
     */
    public function testReprove()
    {
        $priceApproveModelMock = $this->getMockBuilder(PriceApprove::class)
            ->disableOriginalConstructor()
            ->getMock();
        $priceApproveModelMock->expects($this->once())->method('delete')->willReturnSelf();

        $priceApproveModel = $this->getPriceApproveModel();
        $this->assertNull($priceApproveModel->reprove($priceApproveModelMock));
    }

    /**
     * @return void
     * @throws LocalizedException
     */
    public function testSaveByProduct()
    {
        $priceApproveModelMock = $this->getMockBuilder(PriceApprove::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['load', 'hasData', 'getFormattedData', 'getResource'])
            ->addMethods(['insert', 'update'])
            ->getMock();
        $priceApproveModelMock->expects($this->any())->method('load')->willReturnSelf();
        $priceApproveModelMock->expects($this->any())->method('getFormattedData')->willReturn($this->getFormattedData());
        $priceApproveModelMock->expects($this->any())->method('getResource')->willReturnSelf();
        $priceApproveModelMock->expects($this->once())->method('insert')->willReturnSelf();
        $priceApproveModelMock->expects($this->once())->method('update')->willReturnSelf();
        $priceApproveModelMock->method('hasData')->willReturnOnConsecutiveCalls(false, true);

        $this->assertNull($priceApproveModelMock->saveByProduct($this->productMock));
        $this->assertNull($priceApproveModelMock->saveByProduct($this->productMock));
    }

    /**
     * @return void
     * @throws LocalizedException
     */
    public function testGetFormattedData()
    {
        $appState = $this->getMockBuilder(State::class)
            ->disableOriginalConstructor()
            ->getMock();

        $appState->expects($this->once())->method('getAreaCode')->willReturn('graphql');
        $this->context->expects($this->once())->method('getAppState')->willReturn($appState);

        $this->productMock->expects($this->any())->method('getName')->willReturn('Name Product Mock');
        $this->productMock->expects($this->any())->method('getSku')->willReturn('SKUPRODMOCK123');
        $this->productMock->method('getData')->with('price')->willReturn('170.50');
        $this->productMock->method('getOrigData')->with('price')->willReturn('150.25');

        $priceApproveModel = $this->getPriceApproveModel();
        $data = $priceApproveModel->getFormattedData($this->productMock);
        $this->assertEquals('SKUPRODMOCK123', $data[PriceApproveInterface::SKU]);
    }

    /**
     * @return void
     */
    public function testSendMail()
    {
        $priceApproveModel = $this->getPriceApproveModel();
        $this->assertNull($priceApproveModel->sendMail($this->productMock));
    }

    /**
     * @return void
     */
    public function testLoadAll()
    {
        $priceApproveModel = $this->getPriceApproveModel();
        $this->assertIsArray($priceApproveModel->loadAll());
    }

    /**
     * @return void
     * @throws LocalizedException
     */
    public function testValidateUserPermission()
    {
        $appState = $this->getMockBuilder(State::class)
            ->disableOriginalConstructor()
            ->getMock();

        $appState->expects($this->any())->method('getAreaCode')->willReturn('adminhtml');
        $this->context->expects($this->any())->method('getAppState')->willReturn($appState);

        $this->sessionMock->expects($this->any())->method('getUser')->willReturnSelf();
        $this->sessionMock->method('getId')->willReturnOnConsecutiveCalls(123, 321);

        $priceApproveModel = $this->getPriceApproveModel();
        $this->assertTrue($priceApproveModel->validateUserPermission(321));
        $this->assertFalse($priceApproveModel->validateUserPermission(321));
    }

    /**
     * @return array
     */
    public function getFormattedData()
    {
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
