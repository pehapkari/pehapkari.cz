<?php declare(strict_types=1);

namespace Pehapkari\Controller;

use Pehapkari\Registration\Repository\TrainingRegistrationRepository;
use Pehapkari\Training\Entity\Training;
use Pehapkari\Training\Repository\PlaceRepository;
use Pehapkari\Training\Repository\TrainingRepository;
use Pehapkari\Training\Repository\TrainingTermRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class TrainingController extends AbstractController
{
    /**
     * @var PlaceRepository
     */
    private $placeRepository;

    /**
     * @var TrainingRepository
     */
    private $trainingRepository;

    /**
     * @var TrainingTermRepository
     */
    private $trainingTermRepository;

    public function __construct(
        TrainingTermRepository $trainingTermRepository,
        TrainingRepository $trainingRepository,
        PlaceRepository $placeRepository
    ) {
        $this->trainingTermRepository = $trainingTermRepository;
        $this->trainingRepository = $trainingRepository;
        $this->placeRepository = $placeRepository;
    }

    /**
     * @Route(path="/vzdelavej-se/", name="trainings")
     */
    public function trainings(): Response
    {
        return $this->render('training/trainings.twig', [
            'upcoming_training_terms' => $this->trainingTermRepository->getUpcoming(),
            'trainings' => $this->trainingRepository->fetchAll(),

            // hardcoded till the db is up
            // 'finishedTrainingTermCount' => $this->trainingTermRepository->getFinishedCount(),
            'total_training_term_count' => 15,
            //  'finishedParticipantCount' => $this->trainingRegistrationRepository->getFinishedCount(),
            'total_participant_count' => 120,

            // @todo is this needed?
            'places' => $this->placeRepository->fetchAll(),
            'past_terms' => $this->trainingTermRepository->fetchFinished(),
            'past_terms_count' => count($this->trainingTermRepository->fetchFinished()),
        ]);
    }

    /**
     * @Route(path="/zacni-skolit/", name="become_trainer")
     */
    public function start(): Response
    {
        return $this->render('training/become_trainer.twig', [
            'places' => $this->placeRepository->fetchAll(),
        ]);
    }

    /**
     * @Route(path="/kurz/{slug}", name="training_detail")
     */
    public function detail(Training $training): Response
    {
        return $this->render('training/detail.twig', [
            'training' => $training,
            'trainer' => $training->getTrainer(),
            'place' => $training->getNearestTermPlace(),
        ]);
    }
}
