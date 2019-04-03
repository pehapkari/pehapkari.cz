<?php declare(strict_types=1);

namespace Pehapkari\Controller;

use Pehapkari\Exception\ShouldNotHappenException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ConferenceController extends AbstractController
{
    /**
     * @var mixed[]
     */
    private $phpPrague = [];

    /**
     * @param mixed[] $phpPrague
     */
    public function __construct(array $phpPrague)
    {
        $this->phpPrague = $phpPrague;
    }

    /**
     * @Route(path="/php-prague/{year}", name="php_prague")
     */
    public function phpPrague(int $year = 2018): Response
    {
        if (! isset($this->phpPrague[$year])) {
            throw new ShouldNotHappenException();
        }

        $values = $this->phpPrague[$year];
        $values['year'] = $year;

        return $this->render('conference/php_prague.twig', $values);
    }
}
