<?php

namespace App\Tests\Mailer;

use App\Entity\User;
use App\Mailer\Mailer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Swift_Mailer;
use Swift_Mime_SimpleMessage;
use Twig\Environment;

class MailerTest extends TestCase
{
    public function testConfirmationEmail(): void
    {
        $user = new User();
        $user->setEmail('foo@bar.com');

        $swiftMailer = $this->getSwiftMailerMock();
        $swiftMailer
            ->expects($this->once())
            ->method('send')
            ->with(
                $this->callback(
                    function (Swift_Mime_SimpleMessage $message) {
                        $this->assertArrayHasKey('foo@bar.com', $message->getTo());
                        $this->assertArrayHasKey('default.email@from.com', $message->getFrom());

                        $this->assertEquals('Welcome to the micropost app!', $message->getSubject());

                        $this->assertEquals('text/html', $message->getContentType());
                        $this->assertEquals('utf-8', $message->getCharset());

                        return true;
                    }
                )
            );

        $twigEnvironment = $this->getTwigEnvironmentMock();
        $twigEnvironment
            ->expects($this->once())
            ->method('render')
            ->with('email/user_register.html.twig', compact('user'));

        $mailer = new Mailer($swiftMailer, $twigEnvironment, 'default.email@from.com');
        $mailer->sendConfirmationEmail($user);
    }

    /**
     * @return Swift_Mailer|MockObject
     */
    private function getSwiftMailerMock()
    {
        return $this->getMockBuilder(Swift_Mailer::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return Environment|MockObject
     */
    private function getTwigEnvironmentMock()
    {
        return $this->getMockBuilder(Environment::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
