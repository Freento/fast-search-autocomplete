<?php

declare(strict_types=1);

namespace Freento\FastSearchAutocomplete\Console\Command;

use Freento\FastSearchAutocomplete\Model\FillFastSearchName as FillNameModel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Psr\Log\LoggerInterface;

class FillFastSearchName extends Command
{
    /**
     * @var FillNameModel
     */
    private FillNameModel $fillName;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param FillNameModel $fillName
     * @param LoggerInterface $logger
     * @param string|null $name
     */
    public function __construct(FillNameModel $fillName, LoggerInterface $logger, string $name = null)
    {
        $this->fillName = $fillName;
        $this->logger = $logger;

        parent::__construct($name);
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('freento:fsa:fillname');
        $this->setDescription('Fill product\'s Fast Autocomplete Product Name attribute values');
        parent::configure();
    }

    /**
     * CLI command to sets products original name attribute value.
     *
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $status = \Magento\Framework\Console\Cli::RETURN_SUCCESS;
        try {
            $this->fillName->execute();
        } catch (\Exception $e) {
            $this->logger->warning(
                '[Freento][FastSearchAutocomplete][CLI - FillName] : ' . $e->getMessage(),
                $e->getTrace()
            );
            $status = \Magento\Framework\Console\Cli::RETURN_FAILURE;
        }

        return $status;
    }
}
