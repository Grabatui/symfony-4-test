<?php

namespace App\Controller;

use App\Entity\MicroPost;
use App\Entity\User;
use App\Form\MicroPostType;
use App\Repository\MicroPostRepository;
use App\Repository\UserRepository;
use App\Security\MicroPostVoter;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @Route("/micro-post")
 */
class MicroPostController extends AbstractController
{
    /**
     * @var MicroPostRepository
     */
    private MicroPostRepository $microPostRepository;

    /**
     * @var FormFactoryInterface
     */
    private FormFactoryInterface $formFactory;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var FlashBagInterface
     */
    private FlashBagInterface $flashBag;

    /**
     * @var TokenStorageInterface
     */
    private TokenStorageInterface $tokenStorage;

    public function __construct(
        MicroPostRepository $microPostRepository,
        FormFactoryInterface $formFactory,
        EntityManagerInterface $entityManager,
        FlashBagInterface $flashBag,
        TokenStorageInterface $tokenStorage
    )
    {
        $this->microPostRepository = $microPostRepository;
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->flashBag = $flashBag;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @Route("/", name="micro_post_index")
     *
     * @param UserRepository $userRepository
     * @return Response
     */
    public function index(UserRepository $userRepository)
    {
        $currentUser = $this->tokenStorage->getToken()->getUser();

        $usersToFollow = [];
        if ($currentUser instanceof User) {
            $posts = $this->microPostRepository->findAllByUsers($currentUser->getFollowing());

            if (empty($posts)) {
                $usersToFollow = $userRepository->findAllWithMoreThanPosts(1, $currentUser);
            }
        } else {
            $posts = $this->microPostRepository->findBy([], ['time' => 'desc']);
        }

        return $this->render('micro-post/index.html.twig', compact('posts', 'usersToFollow'));
    }

    /**
     * @Route("/add", name="micro_post_add")
     *
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function add(Request $request)
    {
        $this->denyAccessUnlessGranted(User::ROLE_USER);

        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();

        $post = new MicroPost;
        $post->setUser($user);

        $form = $this->formFactory->create(MicroPostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($post);
            $this->entityManager->flush();

            return $this->redirectToRoute('micro_post_index');
        }

        return $this->render('micro-post/add.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/user/{username}", name="micro_post_user")
     *
     * @param User $user
     * @return Response
     */
    public function userPosts(User $user)
    {
        $posts = $this->microPostRepository->findBy(
            ['user' => $user],
            ['time' => 'desc']
        );

        return $this->render('micro-post/user-posts.html.twig', compact('posts', 'user'));
    }

    /**
     * @Route("/{id}", name="micro_post_detail")
     *
     * @param MicroPost $post
     * @return Response
     */
    public function detail(MicroPost $post)
    {
        return $this->render('micro-post/detail.html.twig', compact('post'));
    }

    /**
     * @Route("/{id}/edit", name="micro_post_edit")
     *
     * @param MicroPost $post
     * @param Request $request
     * @return Response
     */
    public function edit(MicroPost $post, Request $request)
    {
        $this->denyAccessUnlessGranted(MicroPostVoter::EDIT, $post);

        $form = $this->formFactory->create(MicroPostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('micro_post_index');
        }

        return $this->render('micro-post/edit.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/{id}/delete", name="micro_post_delete")
     *
     * @param MicroPost $post
     * @return Response
     */
    public function delete(MicroPost $post)
    {
        $this->denyAccessUnlessGranted(MicroPostVoter::DELETE, $post);

        $this->entityManager->remove($post);
        $this->entityManager->flush();

        $this->flashBag->add('notice', 'Micro post was deleted');

        return $this->redirectToRoute('micro_post_index');
    }
}
