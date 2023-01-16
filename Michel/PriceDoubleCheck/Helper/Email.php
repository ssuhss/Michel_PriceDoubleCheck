<?php

namespace Michel\PriceDoubleCheck\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Escaper;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Email extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var StateInterface
     */
    protected StateInterface $inlineTranslation;

    /**
     * @var Escaper
     */
    protected Escaper $escaper;

    /**
     * @var TransportBuilder
     */
    protected TransportBuilder $transportBuilder;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var ScopeConfigInterface
     */
    protected ScopeConfigInterface $config;

    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;

    /**
     * @var
     */
    public $product;

    public function __construct(
        Context               $context,
        StateInterface        $inlineTranslation,
        Escaper               $escaper,
        TransportBuilder      $transportBuilder,
        ScopeConfigInterface  $config,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->inlineTranslation = $inlineTranslation;
        $this->escaper = $escaper;
        $this->transportBuilder = $transportBuilder;
        $this->logger = $context->getLogger();
        $this->storeManager = $storeManager;
        $this->config = $config;
    }

    /**
     * @return void
     */
    public function sendEmail()
    {
        try {
            if (!$this->getEmailConfig('enable')) {
                return;
            }

            $this->inlineTranslation->suspend();

            $transport = $this->transportBuilder->setTemplateIdentifier(
                $this->getEmailConfig('email_template_price_approve')
            )->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_ADMINHTML,
                    'store' => $this->storeManager->getStore()->getStoreId(),
                ]
            )->setTemplateVars(
                [
                    'sku' => $this->getProduct()->getSku()
                ]
            )->setFrom(
                $this->getEmailConfig('email_identity')
            )->addTo(
                $this->getEmailConfig('email_to')
            )->getTransport();

            $transport->sendMessage();

            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
        }
    }

    /**
     * @param $field
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getEmailConfig($field)
    {
        return $this->config->getValue(
            'price_approve/general/' . $field,
            ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getId()
        );
    }

    /**
     * @return mixed
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param mixed $product
     */
    public function setProduct($product): void
    {
        $this->product = $product;
    }
}
