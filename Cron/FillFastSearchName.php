<?php

declare(strict_types=1);

namespace Freento\FastSearchAutocomplete\Cron;

use Freento\FastSearchAutocomplete\Model\FillFastSearchName as FillNameModel;
use Psr\Log\LoggerInterface;

class FillFastSearchName
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
     */
    public function __construct(
        FillNameModel $fillName,
        LoggerInterface $logger
    ) {
        $this->fillName = $fillName;
        $this->logger = $logger;
    }

    /**
     * Set products original name attribute value
     *
     * @return void
     */
    public function execute(): void
    {
        try {
            $this->fillName->execute();
        } catch (\Exception $e) {
            $this->logger->warning(
                '[Freento][FastSearchAutocomplete][Cron - FillName] : ' . $e->getMessage(),
                $e->getTrace()
            );
        }
    }
}
