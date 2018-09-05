<?php declare(strict_types=1);

namespace OpenTraining\Provision\Controller;

use OpenTraining\Training\Entity\TrainingTerm;
use OpenTraining\Training\Repository\TrainingTermRepository;
use OpenTraining\Provision\ProvisionResolver;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

/**
 * @todo admin only
 * @see https://symfony.com/doc/current/controller/service.html#alternatives-to-base-controller-methods
 */
final class ProvisionController
{
    /**
     * @var ProvisionResolver
     */
    private $provisionResolver;

    /**
     * @var EngineInterface
     */
    private $templatingEngine;

    /**
     * @var TrainingTermRepository
     */
    private $trainingTermRepository;
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(
        ProvisionResolver $provisionResolver,
        EngineInterface $templatingEngine,
        TrainingTermRepository $trainingTermRepository,
        RouterInterface $router

    ) {
        $this->provisionResolver = $provisionResolver;
        $this->templatingEngine = $templatingEngine;
        $this->trainingTermRepository = $trainingTermRepository;
        $this->router = $router;
    }



    /**
     * @Route(path="/provision/", name="provision")
     */
    public function default(): Response
    {
        // how to auto-complete:
        // "/packages/Provision/templates/provision/default.twig"
        return $this->templatingEngine->renderResponse('provision/default.twig');
    }









}
