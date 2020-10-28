<?php

namespace App\EventListener;

use App\Event\UserRegisterEvent;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Twig\Environment;

class UserSubscriber implements EventSubscriberInterface
{
    private Swift_Mailer $mailer;

    private Environment $twigEnvironment;

    public function __construct(Swift_Mailer $mailer, Environment $twigEnvironment)
    {
        $this->mailer = $mailer;
        $this->twigEnvironment = $twigEnvironment;
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

        $body = $this->twigEnvironment->render('email/user_register.html.twig', compact('user'));

        $message = (new Swift_Message())
            ->setFrom('micropost@micropost.com')
            ->setTo($user->getEmail())
            ->setSubject('Welcome to the micropost app!')
            ->setBody($body, 'text/html');

        $this->mailer->send($message);
    }
}
