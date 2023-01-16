<?php

declare(strict_types=1);

namespace Michel\PriceDoubleCheck\Console\Command;

use Michel\PriceDoubleCheck\Api\Data\PriceApproveInterface;
use Michel\PriceDoubleCheck\Model\PriceApprove;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListApprovePrice extends Command
{
    protected PriceApprove $priceApprove;
    public function __construct(
        PriceApprove $priceApprove
    ) {
        parent::__construct();
        $this->priceApprove = $priceApprove;
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('michel:price:list');
        $this->setDescription('List all prices to approve.');
        parent::configure();
    }

    /**
     * Execute the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $table = new Table($output);
            $table->setHeaders(['ID', 'Nome', 'SKU', 'Data', 'Atributo', 'Valor Anterior', 'Valor Atual']);
            $allPrices = $this->priceApprove->loadAll();
            foreach ($allPrices as $priceToApprove) {
                $table->addRow([
                    $priceToApprove[PriceApproveInterface::ID],
                    $priceToApprove[PriceApproveInterface::NAME],
                    $priceToApprove[PriceApproveInterface::SKU],
                    $priceToApprove[PriceApproveInterface::DATETIME],
                    $priceToApprove[PriceApproveInterface::ATTRIBUTE],
                    $priceToApprove[PriceApproveInterface::PRICE_BEFORE],
                    $priceToApprove[PriceApproveInterface::PRICE_AFTER_APPROVE]]);
            }

            $table->render();

            return \Magento\Framework\Console\Cli::RETURN_SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
                $output->writeln($e->getTraceAsString());
            }

            return \Magento\Framework\Console\Cli::RETURN_FAILURE;
        }
    }
}
