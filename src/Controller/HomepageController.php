<?php

declare(strict_types=1);

namespace Pehapkari\Controller;

use Pehapkari\Blog\Repository\OrganizerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class HomepageController extends AbstractController
{
    private OrganizerRepository $oragnizerProvider;

    public function __construct(OrganizerRepository $organizerRepository)
    {
        $this->oragnizerProvider = $organizerRepository;
    }

    /**
     * @Route(path="/", name="homepage")
     */
    public function __invoke(): Response
    {
        return $this->render('homepage/homepage.twig', [
            'organizers' => $this->oragnizerProvider->provide(),
        ]);
    }
}
