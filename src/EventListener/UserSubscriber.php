<?php

namespace App\EventListener;

use App\Entity\UserPreferences;
use App\Event\UserRegisterEvent;
use App\Mailer\Mailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserSubscriber implements EventSubscriberInterface
{
    private Mailer $mailer;

    private EntityManagerInterface $entityManager;

    private string $defaultLocale;

    public function __construct(Mailer $mailer, EntityManagerInterface $entityManager, string $defaultLocale)
    {
        $this->mailer = $mailer;
        $this->entityManager = $entityManager;
        $this->defaultLocale = $defaultLocale;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserRegisterEvent::class => [
                ['createUserPreferences', 10],
                ['sendConfirmationEmail', 20],
            ],
        ];
    }

    public function sendConfirmationEmail(UserRegisterEvent $event)
    {
        $user = $event->getRegisteredUser();

        $this->mailer->sendConfirmationEmail($user);
    }

    public function createUserPreferences(UserRegisterEvent $event)
    {
        $user = $event->getRegisteredUser();

        $preferences = new UserPreferences();
        $preferences->setLocale($this->defaultLocale);

        $user->setPreferences($preferences);

        $this->entityManager->flush();
    }
}
