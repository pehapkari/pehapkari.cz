<?php declare(strict_types=1);

namespace Pehapkari\Training\Repository;

use Doctrine\Common\Proxy\Proxy;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Pehapkari\Training\Entity\TrainingTerm;

final class TrainingTermRepository
{
    /**
     * @var EntityRepository
     */
    private $entityRepository;

    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->entityRepository = $entityManager->getRepository(TrainingTerm::class);
    }

    /**
     * @return TrainingTerm[]
     */
    public function fetchFinished(): array
    {
        return $this->entityRepository->createQueryBuilder('tt')
            ->where('tt.endDateTime < CURRENT_DATE()')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return TrainingTerm[]
     */
    public function fetchFinishedWithoutPaidProvision(): array
    {
        return $this->entityRepository->createQueryBuilder('tt')
            ->andWhere('tt.endDateTime < CURRENT_DATE()')
            ->andWhere('tt.isProvisionPaid = false')
            ->getQuery()
            ->getResult();
    }

    public function getFinishedCount(): int
    {
        return (int) $this->entityRepository->createQueryBuilder('tt')
            ->select('count(tt.id)')
            ->andWhere('tt.endDateTime < CURRENT_DATE()')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findBySlug(string $slug): ?TrainingTerm
    {
        return $this->entityRepository->findOneBy([
            'slug' => $slug,
        ]);
    }

    /**
     * @param mixed $id
     * @return bool|Proxy|object|null
     */
    public function getReference($id)
    {
        return $this->entityManager->getReference(TrainingTerm::class, $id);
    }
}
