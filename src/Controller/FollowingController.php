<?php

namespace App\Controller;

use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/following")
 * @IsGranted("ROLE_USER")
 */
class FollowingController extends AbstractController
{
    /**
     * @Route("/follow/{id}", name="following_follow")
     *
     * @param User $user
     * @return RedirectResponse
     */
    public function follow(User $user)
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if ($currentUser->getId() === $user->getId() || $currentUser->getFollowing()->contains($user)) {
            return $this->makeResponse($user);
        }

        $currentUser->getFollowing()->add($user);

        $this->getDoctrine()->getManager()->flush();
        return $this->makeResponse($user);
    }

    /**
     * @Route("/unfollow/{id}", name="following_unfollow")
     *
     * @param User $user
     * @return RedirectResponse
     */
    public function unfollow(User $user)
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if ($currentUser->getId() === $user->getId() || !$currentUser->getFollowing()->contains($user)) {
            return $this->makeResponse($user);
        }

        $currentUser->getFollowing()->removeElement($user);

        $this->getDoctrine()->getManager()->flush();
        return $this->makeResponse($user);
    }

    private function makeResponse(User $user): RedirectResponse
    {
        return $this->redirectToRoute('micro_post_user', ['username' => $user->getUsername()]);
    }
}
