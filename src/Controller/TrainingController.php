<?php declare(strict_types=1);

namespace OpenTraining\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class TrainingController extends AbstractController
{
    /**
     * @Route(path="/vzdelavej-se", name="trainings")
     */
    public function trainings(): Response
    {
        return $this->render('training/trainings.twig');
    }
}
