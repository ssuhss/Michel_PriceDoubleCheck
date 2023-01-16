<?php

declare(strict_types=1);

namespace Michel\PriceDoubleCheck\Console\Command;

use Michel\PriceDoubleCheck\Api\Data\PriceApproveInterface;
use Michel\PriceDoubleCheck\Model\PriceApprove;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Reprove extends Command
{
    private const ID = 'id';
    private const SKU = 'sku';

    protected PriceApprove $priceApprove;

    public function __construct(
        PriceApprove $priceApprove
    ) {
        parent::__construct();
        $this->priceApprove = $priceApprove;
    }

    protected function configure(): void
    {
        $this->setName('michel:price:reprove');
        $this->setDescription('Approve new price.');

        $this->addOption(self::SKU, null, InputOption::VALUE_OPTIONAL, 'Approve by SKU');
        $this->addOption(self::ID, null, InputOption::VALUE_OPTIONAL, 'Approve by ID');

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

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $sku = $input->getOption(self::SKU);
            $id = $input->getOption(self::ID);

            if (empty($sku) && empty($id)) {
                $output->writeln('<error>ID or SKU is required.</error>');
                return \Magento\Framework\Console\Cli::RETURN_FAILURE;
            }

            if (!empty($id)) {
                $priceApproveModel = $this->priceApprove->load($id);
            }

            if (!empty($sku)) {
                $priceApproveModel = $this->priceApprove->load($sku, 'sku');
            }

            if ($priceApproveModel->hasData(PriceApproveInterface::ID)) {
                $this->priceApprove->reprove($priceApproveModel);
                $output->writeln('<info>Price reproved</info>');
                return \Magento\Framework\Console\Cli::RETURN_SUCCESS;
            }

            $output->writeln('<error>Not Found.</error>');
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
                $output->writeln($e->getTraceAsString());
            }
        }
        return \Magento\Framework\Console\Cli::RETURN_FAILURE;
    }
}
