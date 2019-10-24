<?php

declare(strict_types=1);

namespace Pehapkari\Controller;

use Pehapkari\Repository\PhpPragueRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ConferenceController extends AbstractController
{
    /**
     * @Route(path="/php-prague/{year}", name="php_prague")
     */
    public function run(PhpPragueRepository $phpPragueRepository, int $year = 2018): Response
    {
        $values = $phpPragueRepository->findByYear($year);
        $values['year'] = $year;

        return $this->render('conference/php_prague.twig', $values);
    }
}
