<?php

namespace Michel\PriceDoubleCheck\Test\Unit\Controller\Adminhtml\Grid;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\View\Result\PageFactory;
use Michel\PriceDoubleCheck\Controller\Adminhtml\Grid\Approve;
use Michel\PriceDoubleCheck\Model\PriceApprove as ApprovePriceModel;
use PHPUnit\Framework\TestCase;

class ApproveTest extends TestCase
{
    protected Context $contextMock;
    protected PageFactory $resultPageFactoryMock;
    protected ApprovePriceModel $approvePriceModelMock;
    protected RedirectFactory $resultRedirectFactory;
    protected Redirect $resultRedirect;
    protected RequestInterface $requestMock;
    protected ManagerInterface $messageManagerMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setMocks();

        $this->approveGridMock = new Approve(
            $this->contextMock,
            $this->resultPageFactoryMock,
            $this->approvePriceModelMock
        );
    }

    /**
     * @return void
     */
    public function setMocks()
    {
        $this->resultRedirectFactory = $this->getMockBuilder(RedirectFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['create'])
            ->addMethods(['setPath'])
            ->getMock();

        $this->resultRedirect = $this->getMockBuilder(Redirect::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->resultRedirect->method('setPath')->willReturnOnConsecutiveCalls(
            'Something went wrong, Please try again.',
            'Something went wrong, Please try again.',
            "A different user is required to make the change.",
            "Price approved."
        );

        $this->resultRedirectFactory->expects($this->atLeastOnce())
            ->method('create')
            ->willReturn($this->resultRedirect);

        $this->requestMock = $this->getMockForAbstractClass(
            RequestInterface::class,
            [],
            '',
            false,
            true,
            true,
            []
        );

        $this->requestMock->method('getParam')->willReturnOnConsecutiveCalls(null, 10, 20, 40);

        $this->messageManagerMock = $this->getMockForAbstractClass(ManagerInterface::class);

        $this->contextMock = $this->createMock(Context::class);
        $this->contextMock->method('getResultRedirectFactory')->willReturn($this->resultRedirectFactory);
        $this->contextMock->expects($this->once())->method('getRequest')->willReturn($this->requestMock);
        $this->contextMock->expects($this->any())->method('getMessageManager')->willReturn($this->messageManagerMock);

        $this->resultPageFactoryMock = $this->createMock(PageFactory::class);

        $this->approvePriceModelMock = $this->createMock(ApprovePriceModel::class);
        $this->approvePriceModelMock->method('validateUserPermission')->willReturnOnConsecutiveCalls(false, true, true);
        $this->approvePriceModelMock->method('hasData')->willReturnOnConsecutiveCalls(false, true, true);
        $this->approvePriceModelMock->expects($this->any())->method('load')->willReturnSelf();
    }

    /**
     * @return void
     */
    public function testExecute()
    {
        $this->assertEquals('Something went wrong, Please try again.', $this->approveGridMock->execute());
        $this->assertEquals('Something went wrong, Please try again.', $this->approveGridMock->execute());
        $this->assertEquals('A different user is required to make the change.', $this->approveGridMock->execute());
        $this->assertEquals('Price approved.', $this->approveGridMock->execute());
    }
}
