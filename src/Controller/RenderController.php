<?php

declare(strict_types=1);

namespace Pehapkari\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class RenderController extends AbstractController
{
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
