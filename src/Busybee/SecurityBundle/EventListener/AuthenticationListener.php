<?php
namespace Busybee\SecurityBundle\EventListener;

use Busybee\SecurityBundle\BusybeeSecurityEvents;
use Busybee\SecurityBundle\Event\UserEvent;
use Busybee\SecurityBundle\Event\FilterUserResponseEvent;
use Busybee\SecurityBundle\Security\LoginManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Exception\AccountStatusException;

class AuthenticationListener implements EventSubscriberInterface
{
    private $loginManager;
    private $firewallName;

    public function __construct(LoginManagerInterface $loginManager, $firewallName)
    {
        $this->loginManager = $loginManager;
        $this->firewallName = $firewallName;
    }

    public static function getSubscribedEvents()
    {
        return array(
            BusybeeSecurityEvents::REGISTRATION_COMPLETED => 'authenticate',
            BusybeeSecurityEvents::REGISTRATION_CONFIRMED => 'authenticate',
            BusybeeSecurityEvents::RESETTING_RESET_COMPLETED => 'authenticate',
        );
    }

    public function authenticate(FilterUserResponseEvent $event, $eventName = null, EventDispatcherInterface $eventDispatcher = null)
    {
        if (!$event->getUser()->isEnabled()) {
            return;
        }

        try {
            $this->loginManager->loginUser($this->firewallName, $event->getUser(), $event->getResponse());

            $eventDispatcher->dispatch(BusybeeSecurityEvents::SECURITY_IMPLICIT_LOGIN, new UserEvent($event->getUser(), $event->getRequest()));
        } catch (AccountStatusException $ex) {
            // We simply do not authenticate users which do not pass the user
            // checker (not enabled, expired, etc.).
        }
    }
}
