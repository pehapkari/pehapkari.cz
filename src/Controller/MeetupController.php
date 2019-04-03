<?php declare(strict_types=1);

namespace Pehapkari\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class MeetupController extends AbstractController
{
    /**
     * @Route(path="/for-speakers", name="for_speakers")
     */
    public function trainings(): Response
    {
        return $this->render('meetup/for_speakers.twig');
    }
}
