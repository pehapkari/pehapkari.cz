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
        $this->ensureYearIsConfigured($year);

        $values = $this->phpPrague[$year];
        $values['year'] = $year;

        return $this->render('conference/php_prague.twig', $values);
    }

    private function ensureYearIsConfigured(int $year): void
    {
        if (isset($this->phpPrague[$year])) {
            return;
        }

        throw new ShouldNotHappenException(sprintf(
            'Year "%d" was not found. Add it to "%s" file',
            $year,
            'conferences.yaml'
        ));
    }
}
