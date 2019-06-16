<?php declare(strict_types=1);

namespace Pehapkari\Training\Controller;

use Pehapkari\Training\Entity\Place;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class PlaceController extends AbstractController
{
    /**
     * @Route(path="/vzdelavej-se/misto-{slug}", name="place_detail")
     */
    public function detail(Place $place): Response
    {
        return $this->render('place/place_detail.twig', [
            'place' => $place,
        ]);
    }
}
