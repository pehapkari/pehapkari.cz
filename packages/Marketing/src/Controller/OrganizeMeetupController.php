<?php

declare(strict_types=1);

namespace Pehapkari\Marketing\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class OrganizeMeetupController extends AbstractController
{
    /**
     * @Route(path="zoganizujte-sraz", name="organize_meetup")
     */
    public function __invoke(): Response
    {
        return $this->render('marketing/organize_meetup.twig', [
            'organize_meetup_count' => 40 + 20 + 5, // Prague + Brno + rest
        ]);
    }
}
