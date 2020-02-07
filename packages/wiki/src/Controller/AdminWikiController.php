<?php

declare(strict_types=1);

namespace Pehapkari\Wiki\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Pehapkari\Training\Repository\TrainerRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AdminWikiController extends EasyAdminController
{
    /**
     * @var TrainerRepository
     */
    private $trainerRepository;

    public function __construct(TrainerRepository $trainerRepository)
    {
        $this->trainerRepository = $trainerRepository;
    }

    /**
     * @Route(path="admin/wiki/organize-training-term", name="wiki_organize_training_term")
     */
    public function __invoke(): Response
    {
        return $this->render('wiki/organize_training_term.twig', [
            'trainers' => $this->trainerRepository->fetchAllSortedByTrainingTermCount(),
        ]);
    }
}
