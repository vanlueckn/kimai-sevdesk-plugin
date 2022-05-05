<?php

namespace KimaiPlugin\KimaiSevdeskBundle\EventSubscriber;

use App\Event\SystemConfigurationEvent;
use App\Form\Model\Configuration;
use App\Form\Model\SystemConfiguration as SystemConfigurationModel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SystemConfigurationSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            SystemConfigurationEvent::class => ['onSystemConfiguration', 100],
        ];
    }

    public function onSystemConfiguration(SystemConfigurationEvent $event)
    {
        $event->addConfiguration(
            (new SystemConfigurationModel('sevdesk_config'))
                ->setConfiguration([
                    (new Configuration())
                        ->setName('sevdesk.api_key')
                        ->setLabel('sevdesk.api_key')
                        ->setTranslationDomain('system-configuration')
                        ->setRequired(false)
                        ->setType(TextType::class),
                    (new Configuration())
                        ->setName('sevdesk.contact_person_id')
                        ->setLabel('sevdesk.contact_person_id')
                        ->setTranslationDomain('system-configuration')
                        ->setRequired(false)
                        ->setType(TextType::class),
                    (new Configuration())
                        ->setName('sevdesk.tax_rate')
                        ->setLabel('sevdesk.tax_rate')
                        ->setTranslationDomain('system-configuration')
                        ->setRequired(false)
                        ->setType(TextType::class),
                    (new Configuration())
                        ->setName('sevdesk.contact_id')
                        ->setLabel('sevdesk.contact_id')
                        ->setTranslationDomain('system-configuration')
                        ->setRequired(false)
                        ->setType(TextType::class),
                    (new Configuration())
                        ->setName('sevdesk.hour_unit_id')
                        ->setLabel('sevdesk.hour_unit_id')
                        ->setTranslationDomain('system-configuration')
                        ->setRequired(false)
                        ->setType(TextType::class),
                ])
        );
    }
}