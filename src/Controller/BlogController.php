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
     * @Route(path="/blog/", name="blog")
     */
    public function __invoke(PostsProvider $postsProvider, AuthorsProvider $authorsProvider): Response
    {
        return $this->render('blog/blog.twig', [
            'posts' => $postsProvider->provide(),
            'authors' => $authorsProvider->provide(),
            'author_count' => $authorsProvider->getCount(),
        ]);
    }
}
