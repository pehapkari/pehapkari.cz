<?php

declare(strict_types=1);

namespace Pehapkari\Controller;

use Pehapkari\Statie\AuthorsProvider;
use Pehapkari\Statie\PostsProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class BlogController extends AbstractController
{
    /**
     * @var PostsProvider
     */
    private $postsProvider;

    /**
     * @var AuthorsProvider
     */
    private $authorsProvider;

    public function __construct(PostsProvider $postsProvider, AuthorsProvider $authorsProvider)
    {
        $this->postsProvider = $postsProvider;
        $this->authorsProvider = $authorsProvider;
    }

    /**
     * @Route(path="blog", name="blog")
     */
    public function __invoke(): Response
    {
        return $this->render('blog/blog.twig', [
            'posts' => $this->postsProvider->provide(),
            'authors' => $this->authorsProvider->provide(),
            'author_count' => $this->authorsProvider->getCount(),
        ]);
    }
}
