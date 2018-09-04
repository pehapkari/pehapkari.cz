<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\Training;
use App\Repository\PlaceRepository;
use App\Repository\TrainingReferenceRepository;
use App\Repository\TrainingRepository;
use App\Repository\TrainingTermRepository;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class TrainingController
{
    /**
     * @var EngineInterface
     */
    private $templatingEngine;

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
        EngineInterface $templatingEngine,
        TrainingRepository $trainingRepository,
        PlaceRepository $placeRepository,
        TrainingReferenceRepository $trainingReferenceRepository,
        TrainingTermRepository $trainingTermRepository
    ) {
        $this->templatingEngine = $templatingEngine;
        $this->trainingRepository = $trainingRepository;
        $this->placeRepository = $placeRepository;
        $this->trainingReferenceRepository = $trainingReferenceRepository;
        $this->trainingTermRepository = $trainingTermRepository;
    }

    /**
     * @Route(path="/trainings/", name="trainings")
     */
    public function default(): Response
    {
        return $this->templatingEngine->renderResponse('training/default.twig', [
            'trainings' => $this->trainingRepository->fetchAll(),
            'place' => $this->placeRepository->getMainPlace(),
            'references' => $this->trainingReferenceRepository->fetchAll(),
            'referenceCount' => count($this->trainingReferenceRepository->fetchAll()),
            'pastTerms' => $this->trainingTermRepository->fetchFinished(),
            'pastTermsCount' => count($this->trainingTermRepository->fetchFinished()),
        ]);
    }

    /**
     * @Route(path="/training-detail/{training}", name="training-detail")
     */
    public function detail(Training $training): Response
    {
        return $this->templatingEngine->renderResponse('training/detail.twig', [
            'training' => $training,
            'trainer' => $training->getTrainer(),
            'place' => $this->placeRepository->getMainPlace(),
        ]);
    }
}
