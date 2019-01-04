<?php declare(strict_types=1);

namespace OpenTraining\Marketing\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class MarketingController extends AbstractController
{


    public function default(): Response
    {
        // how to auto-complete:
        // "/packages/Provision/templates/provision/default.twig"
        return $this->render('marketing/sponsoring.twig');
    }
}
