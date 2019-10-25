<?php

declare(strict_types=1);

namespace Pehapkari\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class HowToLivestreamController extends AbstractController
{
    /**
     * @Route(path="/for-speakers", name="for_speakers")
     * @Route(path="/how-to-livestream")
     * @Route(path="/stream")
     */
    public function __invoke(): Response
    {
        return $this->render('meetup/for_speakers.twig');
    }
}
