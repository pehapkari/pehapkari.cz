<?php declare(strict_types=1);

namespace Pehapkari\Controller;

use Nette\Utils\Json;
use Pehapkari\Training\Entity\Trainer;
use Pehapkari\Training\Repository\TrainingRepository;
use Pehapkari\Training\Repository\TrainingTermRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ApiController extends AbstractController
{
    /**
     * @var TrainingRepository
     */
    private $trainingRepository;

    /**
     * @var TrainingTermRepository
     */
    private $trainingTermRepository;

    public function __construct(TrainingRepository $trainingRepository, TrainingTermRepository $trainingTermRepository)
    {
        $this->trainingRepository = $trainingRepository;
        $this->trainingTermRepository = $trainingTermRepository;
    }

    /**
     * @Route(path="/api/trainings.json", name="api_trainings")
     */
    public function trainings(): Response
    {
        $trainingsData = [];

        foreach ($this->trainingRepository->fetchAll() as $training) {
            $trainer = $training->getTrainer();

            $trainingData = [
                'id' => $training->getId(),
                'name' => $training->getName(),
                'is_active' => $training->isActive(),
                'duration_in_hours' => $training->getDuration(),
                'capacity' => $training->getCapacity(),
                'price' => $training->getPrice(),
            ];

            if ($training->isActive()) {
                $nearestTerm = $training->getNearestTerm();
                if ($nearestTerm) {
                    $trainingData['nearest_date_time'] = $nearestTerm->getStartDateTime()->format('Y-m-d H:i:s');
                }
            }

            $trainingData = $this->addTrainerData($trainingData, $trainer);

            $trainingsData[] = $trainingData;
        }

        $data = ['trainings' => $trainingsData];

        return $this->prettyJson($data);
    }

    /**
     * @Route(path="/api/training_terms.json", name="api_training_terms")
     */
    public function trainingTerms(): Response
    {
        $trainingTermsData = [];

        foreach ($this->trainingTermRepository->getUpcoming() as $trainingTerm) {
            $training = $trainingTerm->getTraining();
            $trainer = $trainingTerm->getTrainer();

            $trainingTermData = [
                'training' => $trainingTerm->getTrainingName(),
                'start_date_time' => $trainingTerm->getStartDateTime()->format('Y-m-d H:i:s'),
                'deadline_date_time' => $trainingTerm->getDeadlineDateTime()->format('Y-m-d H:i:s'),
                'price' => $trainingTerm->getPrice(),
                'capacity' => $training->getCapacity(),
                'duration_in_hours' => $training->getDuration(),
                'training_id' => $training->getId(),
            ];

            $trainingTermData = $this->addTrainerData($trainingTermData, $trainer);

            $trainingTermsData[] = $trainingTermData;
        }

        $data = ['training_terms' => $trainingTermsData];

        return $this->prettyJson($data);
    }

    /**
     * @param mixed[] $trainingTermsData
     * @return mixed[]
     */
    private function addTrainerData(array $trainingTermsData, Trainer $trainer): array
    {
        $trainingTermsData['trainer'] = [
            'name' => $trainer->getName(),
            'bio' => $trainer->getBio(),
            'phone' => $trainer->getPhone(),
            'email' => $trainer->getEmail(),
        ];

        return $trainingTermsData;
    }

    /**
     * @param mixed[] $data
     */
    private function prettyJson(array $data): JsonResponse
    {
        $json = Json::encode($data, Json::PRETTY);
        return new JsonResponse($json, 200, [], true);
    }
}
