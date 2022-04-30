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

    public function getSevdeskContactPersonId(): int
    {
        return (int)$this->configuration->find('sevdesk.contact_person_id');
    }

    public function getTaxRate(): int
    {
        return (int)$this->configuration->find('sevdesk.tax_rate');
    }

    public function getSevdeskContactId(): int
    {
        return (int)$this->configuration->find('sevdesk.contact_id');
    }

    public function getSevdeskHourUnitId(): int
    {
        return (int)$this->configuration->find('sevdesk.hour_unit_id');
    }

}