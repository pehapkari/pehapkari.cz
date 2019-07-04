<?php

declare(strict_types=1);

namespace Pehapkari\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class MeetupController extends AbstractController
{
    /**
     * @Route(path="/meetups", name="meetups")
     */
    public function meetups(): Response
    {
        return $this->render('meetup/meetups.twig');
    }

    /**
     * @Route(path="/for-speakers", name="for_speakers")
     */
    public function forSpeakers(): Response
    {
        return $this->render('meetup/for_speakers.twig');
    }
}
