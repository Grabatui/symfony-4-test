<?php

namespace App\Controller;

use App\Entity\MicroPost;
use App\Form\MicroPostType;
use App\Repository\MicroPostRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/micro-post")
 */
class MicroPostController extends AbstractController
{
    /**
     * @var MicroPostRepository
     */
    private $microPostRepository;
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
        MicroPostRepository $microPostRepository,
        FormFactoryInterface $formFactory,
        EntityManagerInterface $entityManager
    )
    {
        $this->microPostRepository = $microPostRepository;
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="micro_post_index")
     */
    public function index()
    {
        $posts = $this->microPostRepository->findBy([], ['time' => 'desc']);

        return $this->render('micro-post/index.html.twig', compact('posts'));
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
        $post = new MicroPost;
        $post->setTime(new DateTime());

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
        $form = $this->formFactory->create(MicroPostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($post);
            $this->entityManager->flush();

            return $this->redirectToRoute('micro_post_index');
        }

        return $this->render('micro-post/edit.html.twig', ['form' => $form->createView()]);
    }
}
