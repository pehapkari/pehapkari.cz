<?php declare(strict_types=1);

namespace OpenTraining\Training\Controller;

use OpenTraining\Training\Entity\Training;
use OpenTraining\Training\Repository\PlaceRepository;
use OpenTraining\Training\Repository\TrainingReferenceRepository;
use OpenTraining\Training\Repository\TrainingRepository;
use OpenTraining\Training\Repository\TrainingTermRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class TrainingController extends AbstractController
{
    /**
     * @var TrainingRepository
     */
    private $trainingRepository;

    /**
     * @var PlaceRepository
     */
    private $placeRepository;

    /**
     * @var TrainingReferenceRepository
     */
    private $trainingReferenceRepository;

    /**
     * @var TrainingTermRepository
     */
    private $trainingTermRepository;

    public function __construct(
        TrainingRepository $trainingRepository,
        PlaceRepository $placeRepository,
        TrainingReferenceRepository $trainingReferenceRepository,
        TrainingTermRepository $trainingTermRepository
    ) {
        $this->trainingRepository = $trainingRepository;
        $this->placeRepository = $placeRepository;
        $this->trainingReferenceRepository = $trainingReferenceRepository;
        $this->trainingTermRepository = $trainingTermRepository;
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
     * @Route(path="/vzdelavej-se/", name="trainings")
     */
    public function default(): Response
    {
        return $this->render('training/trainings.twig', [
            'trainings' => $this->trainingRepository->fetchAll(),
            'place' => $this->placeRepository->getMainPlace(),
            'references' => $this->trainingReferenceRepository->fetchAll(),
            'referenceCount' => count($this->trainingReferenceRepository->fetchAll()),
            'pastTerms' => $this->trainingTermRepository->fetchFinished(),
            'pastTermsCount' => count($this->trainingTermRepository->fetchFinished()),
        ]);
    }

    /**
     * @Route(path="/kurz/{slug}", name="training-detail")
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
