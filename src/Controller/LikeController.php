<?php

namespace App\Controller;

use App\Entity\MicroPost;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/likes")
 */
class LikeController extends AbstractController
{
    /**
     * @Route("/like/{id}", name="likes_like")
     *
     * @param MicroPost $microPost
     * @return JsonResponse
     */
    public function like(MicroPost $microPost)
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if (!($currentUser instanceof User)) {
            return $this->json([], JsonResponse::HTTP_UNAUTHORIZED);
        }

        if ($microPost->isOwner($currentUser)) {
            return $this->json(['count' => $microPost->getLikedBy()->count()]);
        }

        $microPost->like($currentUser);

        $this->getDoctrine()->getManager()->flush();

        return $this->json(['count' => $microPost->getLikedBy()->count()]);
    }

    /**
     * @Route("/unlike/{id}", name="likes_unlike")
     *
     * @param MicroPost $microPost
     * @return JsonResponse
     */
    public function unlike(MicroPost $microPost)
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if (!($currentUser instanceof User)) {
            return $this->json([], JsonResponse::HTTP_UNAUTHORIZED);
        }

        if ($microPost->isOwner($currentUser)) {
            return $this->json(['count' => $microPost->getLikedBy()->count()]);
        }

        $microPost->unlike($currentUser);

        $this->getDoctrine()->getManager()->flush();

        return $this->json(['count' => $microPost->getLikedBy()->count()]);
    }
}
