<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="security_login")
     *
     * @param AuthenticationUtils $utils
     * @return Response
     */
    public function login(AuthenticationUtils $utils)
    {
        return $this->render('security/login.html.twig', [
            'last_username' => $utils->getLastUsername(),
            'error' => $utils->getLastAuthenticationError(),
        ]);
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout()
    {

    }

    /**
     * @Route("/confirm/{token}", name="security_confirm")
     *
     * @param string $token
     * @param UserRepository $userRepository
     * @return Response
     */
    public function confirm(string $token, UserRepository $userRepository)
    {
        $authorizedUser = $this->getUser();

        if ($authorizedUser instanceof User) {
            return $this->redirectToRoute('micro_post_index');
        }

        $user = $userRepository->findOneBy([
            'confirmationToken' => $token,
            'enabled' => false,
        ]);

        if ($user instanceof User) {
            $user->setEnabled(true);
            $user->setConfirmationToken('');

            $this->getDoctrine()->getManager()->flush();
        }

        return $this->render('security/confirmation.html.twig', compact('user'));
    }
}
