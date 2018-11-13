<?php declare(strict_types=1);

namespace OpenTraining\Provision\Controller;

use OpenTraining\AutowiredControllerTrait;
use OpenTraining\Provision\ProvisionResolver;
use OpenTraining\Training\Entity\TrainingTerm;
use OpenTraining\Training\Repository\TrainingTermRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @todo admin only
 * @see https://symfony.com/doc/current/controller/service.html#alternatives-to-base-controller-methods
 */
final class ProvisionController
{
    use AutowiredControllerTrait;

    /**
     * @var ProvisionResolver
     */
    private $provisionResolver;

    /**
     * @var TrainingTermRepository
     */
    private $trainingTermRepository;

    public function __construct(
        ProvisionResolver $provisionResolver,
        TrainingTermRepository $trainingTermRepository
    ) {
        $this->provisionResolver = $provisionResolver;
        $this->trainingTermRepository = $trainingTermRepository;
    }

    /**
     * @Route(path="/provision/", name="provision")
     */
    public function default(): Response
    {
        // how to auto-complete:
        // "/packages/Provision/templates/provision/default.twig"
        return $this->render('provision/default.twig', [
            'trainingTerms' => $this->trainingTermRepository->fetchFinishedWithoutPaidProvision(),
        ]);
    }

    /**
     * @Route(path="/provision/", name="provision")
     */
    public function detail(TrainingTerm $trainingTerm): Response
    {
        return $this->render('provision/default.twig', [
            'provision' => $this->provisionResolver->resolveForTrainingTerm($trainingTerm),
        ]);
    }
}
