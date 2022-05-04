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
            (new SystemConfigurationModel())
                ->setSection('sevdesk_config')
                ->setConfiguration([
                    (new Configuration())
                        ->setName('sevdesk.api_key')
                        ->setLabel('Sevdesk API-Key')
                        ->setTranslationDomain('system-configuration')
                        ->setRequired(false)
                        ->setType(TextType::class),
                ])
        );

        $event->addConfiguration(
            (new SystemConfigurationModel())
                ->setSection('sevdesk_config')
                ->setConfiguration([
                    (new Configuration())
                        ->setName('sevdesk.contact_person_id')
                        ->setLabel('Sevdesk Contact Person ID')
                        ->setTranslationDomain('system-configuration')
                        ->setRequired(false)
                        ->setType(TextType::class),
                ])
        );

        $event->addConfiguration(
            (new SystemConfigurationModel())
                ->setSection('sevdesk_config')
                ->setConfiguration([
                    (new Configuration())
                        ->setName('sevdesk.tax_rate')
                        ->setLabel('Sevdesk Tax Rate')
                        ->setTranslationDomain('system-configuration')
                        ->setRequired(false)
                        ->setType(TextType::class),
                ])
        );

        $event->addConfiguration(
            (new SystemConfigurationModel())
                ->setSection('sevdesk_config')
                ->setConfiguration([
                    (new Configuration())
                        ->setName('sevdesk.contact_id')
                        ->setLabel('Sevdesk Contact ID')
                        ->setTranslationDomain('system-configuration')
                        ->setRequired(false)
                        ->setType(TextType::class),
                ])
        );

        $event->addConfiguration(
            (new SystemConfigurationModel())
                ->setSection('sevdesk_config')
                ->setConfiguration([
                    (new Configuration())
                        ->setName('sevdesk.hour_unit_id')
                        ->setLabel('Sevdesk Hour Unit ID')
                        ->setTranslationDomain('system-configuration')
                        ->setRequired(false)
                        ->setType(TextType::class),
                ])
        );
    }
}