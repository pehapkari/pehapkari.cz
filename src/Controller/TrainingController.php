<?php declare(strict_types=1);

namespace OpenTraining\Controller;

use OpenTraining\Registration\Repository\TrainingRegistrationRepository;
use OpenTraining\Training\Entity\Training;
use OpenTraining\Training\Repository\PlaceRepository;
use OpenTraining\Training\Repository\TrainingRepository;
use OpenTraining\Training\Repository\TrainingTermRepository;
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

    /**
     * @var TrainingRegistrationRepository
     */
    private $trainingRegistrationRepository;

    public function __construct(
        TrainingTermRepository $trainingTermRepository,
        TrainingRegistrationRepository $trainingRegistrationRepository,
        TrainingRepository $trainingRepository,
        PlaceRepository $placeRepository
    ) {
        $this->trainingTermRepository = $trainingTermRepository;
        $this->trainingRegistrationRepository = $trainingRegistrationRepository;
        $this->trainingRepository = $trainingRepository;
        $this->placeRepository = $placeRepository;
    }

    /**
     * @Route(path="/vzdelavej-se/", name="trainings")
     */
    public function trainings(): Response
    {
        return $this->render('training/trainings.twig', [
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
            'place' => $this->placeRepository->getMainPlace(),
        ]);
    }
}
