<?php

namespace KimaiPlugin\KimaiSevdeskBundle\EventSubscriber;

use App\Event\ThemeEvent;
use App\Plugin\Plugin;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class PluginActionSubscriber
{
    /**
     * @var UrlGeneratorInterface
     */
    private $router;
    /**
     * @var AuthorizationCheckerInterface
     */
    private $security;

    /**
     * @param UrlGeneratorInterface $router
     */
    public function __construct(UrlGeneratorInterface $router, AuthorizationCheckerInterface $security)
    {
        $this->router = $router;
        $this->security = $security;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'actions.plugin' => ['onPluginEvent'],
        ];
    }

    public function onPluginEvent(ThemeEvent $event)
    {
        $payload = $event->getPayload();

        if (!isset($payload['actions']) || !isset($payload['plugin'])) {
            return;
        }

        /** @var Plugin $plugin */
        $plugin = $payload['plugin'];

        if ($plugin->getId() !== 'KimaiSevdeskBundle') {
            return;
        }

        $payload['actions']['divider'] = null;

        $payload['actions']['settings'] = [
            'url' => $this->router->generate('system_configuration_section', ['section' => 'sevdesk_config']),
            'class' => 'modal-ajax-form',
        ];

        $event->setPayload($payload);
    }
}