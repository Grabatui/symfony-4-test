<?php

namespace App\Security;

use App\Entity\MicroPost;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class MicroPostVoter extends Voter
{
    public const EDIT = 'edit';
    public const DELETE = 'delete';

    public const ACTIONS = [self::EDIT, self::DELETE];

    protected function supports($attribute, $subject): bool
    {
        if (!in_array($attribute, self::ACTIONS)) {
            return false;
        }

        if (!($subject instanceof MicroPost)) {
            return false;
        }

        return true;
    }

    /**
     * @param string $attribute
     * @param MicroPost $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!($user instanceof User)) {
            return false;
        }

        return $subject->getUser()->getId() === $user->getId();
    }
}
