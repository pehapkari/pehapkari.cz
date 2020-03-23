<?php

declare(strict_types=1);

namespace Pehapkari\Controller;

use Pehapkari\Blog\Repository\OrganizerRepository;
use Pehapkari\Meetup\DataProvider\NearestMeetupProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class HomepageController extends AbstractController
{
    private OrganizerRepository $oragnizerProvider;

    private NearestMeetupProvider $nearestMeetupProvider;

    public function __construct(OrganizerRepository $organizerRepository, NearestMeetupProvider $nearestMeetupProvider)
    {
        $this->oragnizerProvider = $organizerRepository;
        $this->nearestMeetupProvider = $nearestMeetupProvider;
    }

    /**
     * @Route(path="/", name="homepage")
     */
    public function __invoke(): Response
    {
        return $this->render('homepage/homepage.twig', [
            'organizers' => $this->oragnizerProvider->provide(),
            'nearest_meetup' => $this->nearestMeetupProvider->provide(),
        ]);
    }
}
