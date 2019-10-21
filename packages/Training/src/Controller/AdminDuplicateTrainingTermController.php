<?php

declare(strict_types=1);

namespace Pehapkari\Training\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Pehapkari\Training\Entity\TrainingTerm;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AdminDuplicateTrainingTermController extends EasyAdminController
{
    /**
     * @Route(path="/admin/duplicate-training-term/{id}", name="duplicate_training_term", methods={"GET", "POST"})
     */
    public function run(TrainingTerm $trainingTerm): Response
    {
        dump($trainingTerm);
        die;
    }
}
