<?php

declare(strict_types=1);

namespace Pehapkari\Controller;

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
     * @param mixed[] $organizers
     */
    public function __construct(array $organizers)
    {
        $this->organizers = $organizers;
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
     * @Route(path="/privacy-policy/", name="privacy_policy")
     */
    public function privacyPolicy(): Response
    {
        return $this->render('homepage/privacy_policy.twig');
    }

    /**
     * @Route(path="/kontakt/", name="contact")
     * @Route(path="/contact/")
     */
    public function contact(): Response
    {
        return $this->render('homepage/contact.twig');
    }
}
