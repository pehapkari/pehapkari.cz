<?php declare(strict_types=1);

namespace Pehapkari\Training\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class PlaceController extends AbstractController
{
    /**
     * @Route(path="/vzdelavej-se/misto-jablotron-holesovice/", name="place_detail")
     */
    public function detail(): Response
    {
        return $this->render('place/place_detail.twig');
    }
}
