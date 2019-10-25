<?php

declare(strict_types=1);

namespace Pehapkari\Marketing\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class MarketingSponsoringController extends AbstractController
{
    /**
     * @Route(path="/zviditelnete-vasi-firmu", name="sponsoring")
     */
    public function __invoke(): Response
    {
        return $this->render('marketing/sponsoring.twig');
    }
}
