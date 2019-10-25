<?php

declare(strict_types=1);

namespace Pehapkari\Training\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Nette\Utils\DateTime;
use Pehapkari\Training\Entity\TrainingTerm;
use Pehapkari\Training\Repository\TrainingTermRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AdminDuplicateTrainingTermController extends EasyAdminController
{
    /**
     * @var TrainingTermRepository
     */
    private $trainingTermRepository;

    public function __construct(TrainingTermRepository $trainingTermRepository)
    {
        $this->trainingTermRepository = $trainingTermRepository;
    }

    /**
     * @Route(path="/admin/duplicate-training-term/{id}", name="duplicate_training_term")
     */
    public function __invoke(TrainingTerm $trainingTerm): Response
    {
        $trainingTerm = clone $trainingTerm;

        $monthInTheFutureDateTime = $this->createDateTimeMonthInTheFutureWithCurrentTime($trainingTerm);
        $trainingTerm->setStartDateTime($monthInTheFutureDateTime);
        $trainingTerm->setId(null);

        $this->trainingTermRepository->save($trainingTerm);

        return $this->redirectToRoute('easyadmin', [
            'action' => 'edit',
            'entity' => 'TrainingTerm',
            'id' => $trainingTerm->getId(),
        ]);
    }

    private function createDateTimeMonthInTheFutureWithCurrentTime(TrainingTerm $trainingTerm): DateTime
    {
        $currentStartDateTime = $trainingTerm->getStartDateTime();

        $monthInTheFutureDateTime = DateTime::from('+ 30 days');
        $monthInTheFutureDateTime->setTime(
            (int) $currentStartDateTime->format('H'),
            (int) $currentStartDateTime->format('i')
        );
        return $monthInTheFutureDateTime;
    }
}
