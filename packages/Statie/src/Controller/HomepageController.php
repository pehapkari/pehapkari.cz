<?php declare(strict_types=1);

namespace OpenTraining\Statie\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

final class HomepageController extends AbstractController
{
    /**
     * @Route(path="/", name="homepage")
     */
    public function homepage()
    {
        die;
    }
}
