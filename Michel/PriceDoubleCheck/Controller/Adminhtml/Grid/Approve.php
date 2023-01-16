<?php

namespace Michel\PriceDoubleCheck\Controller\Adminhtml\Grid;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Michel\PriceDoubleCheck\Model\PriceApprove;

class Approve extends Action
{
    protected PageFactory $resultPageFactory;
    protected PriceApprove $priceApprove;

    public function __construct(
        Context      $context,
        PageFactory  $resultPageFactory,
        PriceApprove $priceApprove
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->priceApprove = $priceApprove;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirectFactory = $this->resultRedirectFactory->create();
        try {
            $id = (int)$this->getRequest()->getParam('id');
            if (!$id) {
                throw new \Exception("Something went wrong, Please try again.");
            }
            $model = $this->priceApprove->load($id);
            if (!$model->hasData('id')) {
                throw new \Exception("Something went wrong, Please try again.");
            }

            if (!$this->priceApprove->validateUserPermission($model->getAdminUserId())) {
                throw new \Exception("A different user is required to make the change.");
            }

            $this->priceApprove->approve($model);
            $this->messageManager->addSuccessMessage(__("Price approved."));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $resultRedirectFactory->setPath('*/*/index');
    }
}
