<?php

declare(strict_types=1);

namespace Pehapkari\Controller;

use Pehapkari\Statie\AuthorsProvider;
use Pehapkari\Statie\PostsProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class RssController extends AbstractController
{
    /**
     * @Route(path="/rss.xml", name="rss")
     */
    public function __invoke(PostsProvider $postsProvider, AuthorsProvider $authorsProvider): Response
    {
        $response = $this->render('homepage/rss.xml.twig', [
            'posts' => $postsProvider->provide(),
            'authors' => $authorsProvider->provide(),
        ]);

        $response->headers->set('Content-Type', 'xml');

        return $response;
    }
}
