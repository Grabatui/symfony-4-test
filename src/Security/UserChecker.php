<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\LockedException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    /**
     * @inheritDoc
     */
    public function checkPreAuth(UserInterface $user)
    {
        if (!($user instanceof User)) {
            return;
        }

        if (!$user->isEnabled()) {
            throw new LockedException();
        }
    }

    /**
     * @inheritDoc
     */
    public function checkPostAuth(UserInterface $user)
    {

    }
}
