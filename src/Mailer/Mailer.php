<?php

namespace App\Mailer;

use App\Entity\User;
use Swift_Mailer;
use Swift_Message;
use Twig\Environment;

class Mailer
{
    private Swift_Mailer $mailer;

    private Environment $twigEnvironment;

    private string $defaultEmailFrom;

    public function __construct(Swift_Mailer $mailer, Environment $twigEnvironment, string $defaultEmailFrom)
    {
        $this->mailer = $mailer;
        $this->twigEnvironment = $twigEnvironment;
        $this->defaultEmailFrom = $defaultEmailFrom;
    }

    public function sendConfirmationEmail(User $user)
    {
        $body = $this->twigEnvironment->render('email/user_register.html.twig', compact('user'));

        $message = (new Swift_Message())
            ->setFrom($this->defaultEmailFrom)
            ->setTo($user->getEmail())
            ->setSubject('Welcome to the micropost app!')
            ->setBody($body, 'text/html');

        $this->mailer->send($message);
    }
}
