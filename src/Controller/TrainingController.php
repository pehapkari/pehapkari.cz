<?php declare(strict_types=1);

namespace Pehapkari\Controller;

use Pehapkari\Registration\Repository\TrainingRegistrationRepository;
use Pehapkari\Training\Entity\Training;
use Pehapkari\Training\Repository\PlaceRepository;
use Pehapkari\Training\Repository\TrainingFeedbackRepository;
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

    /**
     * @var TrainingFeedbackRepository
     */
    private $trainingFeedbackRepository;

    public function __construct(
        TrainingTermRepository $trainingTermRepository,
        TrainingRepository $trainingRepository,
        PlaceRepository $placeRepository,
        TrainingFeedbackRepository $trainingFeedbackRepository
    ) {
        $this->trainingTermRepository = $trainingTermRepository;
        $this->trainingRepository = $trainingRepository;
        $this->placeRepository = $placeRepository;
        $this->trainingFeedbackRepository = $trainingFeedbackRepository;
    }

    /**
     * @Route(path="/vzdelavej-se/", name="trainings")
     */
    public function trainings(): Response
    {
        $averageRating = $this->trainingFeedbackRepository->getAverageRating();

        return $this->render('training/trainings.twig', [
            'upcoming_training_terms' => $this->trainingTermRepository->getUpcoming(),
            'inactive_trainings' => $this->trainingRepository->fetchInactiveTrainings(),

            // hardcoded till the db is up
            // 'finishedTrainingTermCount' => $this->trainingTermRepository->getFinishedCount(),
            'total_training_term_count' => 15,
            //  'finishedParticipantCount' => $this->trainingRegistrationRepository->getFinishedCount(),
            'total_participant_count' => 120,

            'feedbacks' => $this->trainingFeedbackRepository->getForMainPage(),

            'average_training_rating' => $averageRating,
            'average_training_rating_stars' => round($averageRating, 0),

            'places' => $this->placeRepository->fetchActive(),
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
            'places' => $this->placeRepository->fetchActive(),
        ]);
    }

    /**
     * @Route(path="/kurz/{slug}", name="training_detail")
     */
    public function detail(Training $training): Response
    {
        return $this->render('training/training_detail.twig', [
            'training' => $training,
            'trainer' => $training->getTrainer(),
            'place' => $training->getNearestTermPlace(),
        ]);
    }
}
