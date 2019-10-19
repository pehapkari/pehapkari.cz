<?php

declare(strict_types=1);

namespace Pehapkari\Provision\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Pehapkari\Provision\ProvisionResolver;
use Pehapkari\Training\Entity\TrainingTerm;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AdminTrainingTermProvisionController extends EasyAdminController
{
    /**
     * @var ProvisionResolver
     */
    private $provisionResolver;

    public function __construct(ProvisionResolver $provisionResolver)
    {
        $this->provisionResolver = $provisionResolver;
    }

    /**
     * @Route(path="/admin/provision/{id}", name="training_term_provision")
     */
    public function trainingTermProvision(TrainingTerm $trainingTerm): Response
    {
        $provision = $this->provisionResolver->resolveForTrainingTerm($trainingTerm);

        return $this->render('provision/training_term_provision.twig', [
            'trainer' => $trainingTerm->getTrainer(),
            'provision' => $provision,
            'training' => $trainingTerm->getTraining(),
            'training_term' => $trainingTerm,
        ]);
    }
}
