<?php declare(strict_types=1);

namespace OpenTraining\Provision\Repository;

use App\Entity\TrainingTerm;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Expr\Join;
use OpenTraining\Provision\Entity\Partner;
use OpenTraining\Provision\Entity\PartnerExpense;

final class PartnerRepository
{
    /**
     * @var EntityRepository
     */
    private $entityRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityRepository = $entityManager->getRepository(Partner::class);
    }

    /**
     * @return Partner[]
     */
    public function fetchAll(): array
    {
        return $this->entityRepository->findAll();
    }

    /**
     * @todo value object
     *
     * @return mixed[]
     */
    public function fetchAllWithExpenseForTrainingTerm(TrainingTerm $trainingTerm): array
    {
        $exprBuilder = new Expr();

        $result = $this->entityRepository->createQueryBuilder('p')
            ->leftJoin(
                PartnerExpense::class,
                'pe',
                Join::WITH,
                $exprBuilder->andX(
                    $exprBuilder->eq('pe.trainingTerm', $trainingTerm->getId()),
                    $exprBuilder->eq('pe.partner', 'p.id')
                )
            )
            ->select('p, SUM(pe.amount) AS expense')
            ->groupBy('p.id')
            ->getQuery()
            ->getResult();

        // @todo value object
        dump($result);
        die;
    }
}
