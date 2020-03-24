<?php

declare(strict_types=1);

namespace Pehapkari\Blog\Controller;

use Pehapkari\Blog\Repository\AuthorRepository;
use Pehapkari\Blog\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class RssController extends AbstractController
{
    private AuthorRepository $authorRepository;

    private PostRepository $postRepository;

    public function __construct(AuthorRepository $authorRepository, PostRepository $postRepository)
    {
        $this->authorRepository = $authorRepository;
        $this->postRepository = $postRepository;
    }

    /**
     * @Route(path="rss.xml", name="rss")
     */
    public function __invoke(): Response
    {
        $response = $this->render('blog/rss.xml.twig', [
            'posts' => $this->postRepository->fetchAll(),
            'authors' => $this->authorRepository->fetchAll(),
        ]);

        $response->headers->set('Content-Type', 'xml');

        return $response;
    }
}
