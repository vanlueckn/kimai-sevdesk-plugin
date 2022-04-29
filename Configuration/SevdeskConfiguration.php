<?php

namespace KimaiPlugin\KimaiSevdesk\Configuration;

use App\Configuration\SystemConfiguration;

final class SevdeskConfiguration
{
    private SystemConfiguration $configuration;

    public function __construct(SystemConfiguration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function getSevdeskApiKey(): string
    {
        return (string)$this->configuration->find('sevdesk.api_key');
    }

}