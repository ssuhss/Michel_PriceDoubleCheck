<?php

namespace Michel\PriceDoubleCheck\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Michel\PriceDoubleCheck\Model\PriceApprove;

class DoubleCheckObserver implements ObserverInterface
{
    /**
     * @var PriceApprove
     */
    protected PriceApprove $priceApprove;

    /**
     * @param PriceApprove $priceApprove
     */
    public function __construct(PriceApprove $priceApprove)
    {
        $this->priceApprove = $priceApprove;
    }

    /**
     * @param Observer $observer
     * @return $this|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer)
    {
        $product = $observer->getProduct();
        if (!$product->hasDataChanges()) {
            return $this;
        }

        if ($product->getData('price') != $product->getOrigData('price')) {
            $this->priceApprove->saveByProduct($product);
            $this->priceApprove->sendMail($product);
            $product->setData('price', $product->getOrigData('price'));
        }
    }
}
