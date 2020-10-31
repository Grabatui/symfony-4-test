<?php

namespace App\EventListener;

use App\Event\UserRegisterEvent;
use App\Mailer\Mailer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserSubscriber implements EventSubscriberInterface
{
    private Mailer $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserRegisterEvent::class => 'onUserRegister',
        ];
    }

    public function onUserRegister(UserRegisterEvent $event)
    {
        $user = $event->getRegisteredUser();

        $this->mailer->sendConfirmationEmail($user);
    }
}
