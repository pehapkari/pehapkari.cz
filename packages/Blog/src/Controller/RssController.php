<?php

declare(strict_types=1);

namespace Pehapkari\Blog\Controller;

use Pehapkari\Blog\DataProvider\AuthorsProvider;
use Pehapkari\Blog\DataProvider\PostsProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class RssController extends AbstractController
{
    /**
     * @var AuthorsProvider
     */
    private $authorsProvider;

    /**
     * @var PostsProvider
     */
    private $postsProvider;

    public function __construct(AuthorsProvider $authorsProvider, PostsProvider $postsProvider)
    {
        $this->authorsProvider = $authorsProvider;
        $this->postsProvider = $postsProvider;
    }

    /**
     * @Route(path="rss.xml", name="rss")
     */
    public function __invoke(): Response
    {
        $response = $this->render('homepage/rss.xml.twig', [
            'posts' => $this->postsProvider->provide(),
            'authors' => $this->authorsProvider->provide(),
        ]);

        $response->headers->set('Content-Type', 'xml');

        return $response;
    }
}
