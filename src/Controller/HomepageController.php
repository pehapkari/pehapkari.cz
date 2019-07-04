<?php

declare(strict_types=1);

namespace Pehapkari\Controller;

use Pehapkari\Statie\PostsProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class HomepageController extends AbstractController
{
    /**
     * @var mixed[]
     */
    private $organizers = [];

    /**
     * @var mixed[]
     */
    private $authors = [];

    /**
     * @param mixed[] $organizers
     * @param mixed[] $authors
     */
    public function __construct(array $organizers, array $authors)
    {
        $this->organizers = $organizers;
        $this->authors = $authors;
    }

    /**
     * @Route(path="/", name="homepage")
     */
    public function homepage(): Response
    {
        return $this->render('homepage/homepage.twig', [
            'organizers' => $this->organizers,
        ]);
    }

    /**
     * @Route(path="/about/", name="about")
     */
    public function about(): Response
    {
        return $this->render('homepage/about.twig');
    }

    /**
     * @Route(path="/press/", name="press")
     */
    public function press(): Response
    {
        return $this->render('homepage/press.twig');
    }

    /**
     * @Route(path="/privacy-policy/", name="privacy_policy")
     */
    public function privacyPolicy(): Response
    {
        return $this->render('homepage/privacy_policy.twig');
    }

    /**
     * @Route(path="/contact/", name="contact")
     */
    public function contact(): Response
    {
        return $this->render('homepage/contact.twig');
    }

    /**
     * @Route(path="/rss.xml", name="rss")
     */
    public function rss(PostsProvider $postsProvider): Response
    {
        $response = $this->render('homepage/rss.xml.twig', [
            'posts' => $postsProvider->provide(),
            'authors' => $this->authors,
        ]);

        $response->headers->set('Content-Type', 'xml');

        return $response;
    }
}
