<?php

declare(strict_types=1);

namespace Michel\PriceDoubleCheck\Model;

use Magento\Backend\Model\Auth\Session;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\App\Area;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use Michel\PriceDoubleCheck\Api\Data\PriceApproveInterface;
use Michel\PriceDoubleCheck\Helper\Email as EmailHelper;
use Michel\PriceDoubleCheck\Model\ResourceModel\PriceApprove as PriceApproveResourceModel;

class PriceApprove extends AbstractModel implements PriceApproveInterface
{
    protected Session $authSession;
    protected EmailHelper $emailHelper;
    protected ProductFactory $productFactory;
    protected StoreManagerInterface $storeManager;

    public function __construct(
        Context               $context,
        Registry              $registry,
        ProductFactory        $productFactory,
        StoreManagerInterface $storeManager,
        Session               $authSession,
        EmailHelper           $emailHelper,
        AbstractResource      $resource = null,
        AbstractDb            $resourceCollection = null,
        array                 $data = []
    ) {
        $this->authSession = $authSession;
        $this->emailHelper = $emailHelper;
        $this->productFactory = $productFactory;
        $this->storeManager = $storeManager;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(PriceApproveResourceModel::class);
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return (int)$this->getData(PriceApproveInterface::ID);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return (string)$this->getData(PriceApproveInterface::NAME);
    }

    /**
     * @return string
     */
    public function getSku(): string
    {
        return (string)$this->getData(PriceApproveInterface::SKU);
    }

    /**
     * @return string
     */
    public function getDatetime(): string
    {
        return (string)$this->getData(PriceApproveInterface::DATETIME);
    }

    /**
     * @return string
     */
    public function getAttributeCode(): string
    {
        return (string)$this->getData(PriceApproveInterface::ATTRIBUTE);
    }

    /**
     * @return string
     */
    public function getPriceBefore(): string
    {
        return (string)$this->getData(PriceApproveInterface::PRICE_BEFORE);
    }

    /**
     * @return string
     */
    public function getPriceAfterApprove(): string
    {
        return (string)$this->getData(PriceApproveInterface::PRICE_AFTER_APPROVE);
    }

    public function getUserId(): int
    {
        return (int)$this->getData(PriceApproveInterface::USER_ID);
    }

    /**
     * @param PriceApprove $priceApproveModel
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function approve(self $priceApproveModel): void
    {
        $productFactory = $this->productFactory->create();
        $product = $productFactory->loadByAttribute('sku', $priceApproveModel->getSku());
        if ($product->hasData('entity_id')) {
            $product->addAttributeUpdate('price', $priceApproveModel->getPriceAfterApprove(), $this->storeManager->getStore()->getId());
            $priceApproveModel->delete();
        }
    }

    /**
     * @param PriceApprove $priceApproveModel
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function reprove(self $priceApproveModel): void
    {
        $productFactory = $this->productFactory->create();
        $product = $productFactory->loadByAttribute('sku', $priceApproveModel->getSku());
        if ($product->hasData('entity_id')) {
            $priceApproveModel->delete();
        }
    }

    /**
     * @param $product
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveByProduct($product): void
    {
        $priceApproveModel = $this->load($product->getSku(), 'sku');
        $data = $this->getFormattedData($product);
        if (!$priceApproveModel->hasData('id')) {
            $this->getResource()->insert($data);
            return;
        }
        $data[PriceApproveInterface::ID] = $priceApproveModel->getId();
        $this->getResource()->update($data);
    }

    /**
     * @param $product
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getFormattedData($product): array
    {
        $userId = 0;
        $areaCode = $this->_appState->getAreaCode();
        if ($areaCode != Area::AREA_GRAPHQL) {
            $userId = $this->authSession->getUser()->getId();
        }

        $data[PriceApproveInterface::NAME] = $product->getName();
        $data[PriceApproveInterface::SKU] = $product->getSku();
        $data[PriceApproveInterface::DATETIME] = (new \DateTime('now'))->format('Y-m-d H:i:s');
        $data[PriceApproveInterface::ATTRIBUTE] = 'price';
        $data[PriceApproveInterface::PRICE_BEFORE] = $product->getOrigData('price');
        $data[PriceApproveInterface::PRICE_AFTER_APPROVE] = $product->getData('price');
        $data[PriceApproveInterface::USER_ID] = $userId;

        return $data;
    }

    /**
     * @param $product
     * @return void
     */
    public function sendMail($product): void
    {
        $this->emailHelper->setProduct($product);
        $this->emailHelper->sendEmail();
    }

    /**
     * @return array
     */
    public function loadAll(): array
    {
        return $this->getResource()->fetchAll();
    }



    /**
     * @param $userId
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validateUserPermission($userId): bool
    {
        $areaCode = $this->_appState->getAreaCode();
        if ($areaCode == Area::AREA_ADMINHTML) {
            if ($userId != $this->authSession->getUser()->getId()) {
                return true;
            }
        }
        return false;
    }
}
