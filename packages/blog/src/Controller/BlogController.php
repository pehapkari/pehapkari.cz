<?php

declare(strict_types=1);

namespace Pehapkari\Blog\Controller;

use Pehapkari\Blog\Repository\AuthorRepository;
use Pehapkari\Blog\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class BlogController extends AbstractController
{
    private PostRepository $postRepository;

    private AuthorRepository $authorsProvider;

    public function __construct(PostRepository $postRepository, AuthorRepository $authorRepository)
    {
        $this->postRepository = $postRepository;
        $this->authorsProvider = $authorRepository;
    }

    /**
     * @Route(path="blog", name="blog")
     */
    public function __invoke(): Response
    {
        return $this->render('blog/blog.twig', [
            'posts' => $this->postRepository->fetchAll(),
            'authors' => $this->authorsProvider->fetchAll(),
            'author_count' => $this->authorsProvider->getCount(),
        ]);
    }
}
