<?php

declare(strict_types=1);

namespace Pehapkari\Controller;

use Pehapkari\Statie\OragnizerProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class HomepageController extends AbstractController
{
    /**
     * @Route(path="/", name="homepage")
     */
    public function __run(OragnizerProvider $oragnizerProvider): Response
    {
        return $this->render('homepage/homepage.twig', [
            'organizers' => $oragnizerProvider->provide(),
        ]);
    }
}
