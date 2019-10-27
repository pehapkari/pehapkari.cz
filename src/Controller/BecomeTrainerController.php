<?php

declare(strict_types=1);

namespace Pehapkari\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class BecomeTrainerController extends AbstractController
{
    /**
     * @Route(path="zacni-skolit", name="become_trainer")
     */
    public function __invoke(): Response
    {
        return $this->render('training/become_trainer.twig', []);
    }
}
