<?php declare(strict_types=1);

namespace OpenTraining\Statie\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class HomepageController extends AbstractController
{
    /**
     * @var mixed[]
     */
    private $organizers;

    /**
     * @param mixed[] $organizers
     */
    public function __construct(array $organizers)
    {
        $this->organizers = $organizers;
    }

    /**
     * @Route(path="/", name="homepage")
     */
    public function homepage(): Response
    {
        return $this->render('default/homepage.html.twig', [
            'organizers' => $this->organizers
        ]);
    }
}
