<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/blog")
 */
class BlogController extends AbstractController
{
    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("/", name="blog_index")
     *
     * @return Response
     */
    public function index()
    {
        $posts = $this->session->get('posts');

        return $this->render('blog/index.html.twig', compact('posts'));
    }

    /**
     * @Route("/add", name="blog_add")
     */
    public function add()
    {
        $posts = $this->session->get('posts');

        $posts[uniqid()] = [
            'title' => 'A random title ' . mt_rand(1, 500),
            'text' => 'A random text ' . mt_rand(1, 500),
        ];

        $this->session->set('posts', $posts);

        return $this->redirectToRoute('blog_index');
    }

    /**
     * @Route("/show/{id}", name="blog_show")
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $posts = $this->session->get('posts');

        if (!$posts || !array_key_exists($id, $posts)) {
            throw new NotFoundHttpException('Post not found');
        }

        return $this->render('blog/detail.html.twig', [
            'id' => $id,
            'post' => $posts[$id],
        ]);
    }
}
