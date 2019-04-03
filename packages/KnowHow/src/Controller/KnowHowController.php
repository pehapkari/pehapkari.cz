<?php declare(strict_types=1);

namespace Pehapkari\KnowHow\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class KnowHowController extends AbstractController
{
    /**
     * @Route(path="/know-how/organizace-skoleni/", name="organize_training")
     */
    public function organizeTraining(): Response
    {
        return $this->render('know-how/organize_training.twig');
    }
}
