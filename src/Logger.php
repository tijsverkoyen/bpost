<?php

namespace Bpost\BpostApiClient;

use Psr\Log\LoggerInterface;

/**
 * Class Logger
 * @package Bpost\BpostApiClient
 */
class Logger
{
    /** @var  LoggerInterface */
    private $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param       $message
     * @param array $context
     */
    public function debug($message, array $context = array())
    {
        if ($this->logger) {
            $this->logger->debug($message, $context);
        }
    }
}
