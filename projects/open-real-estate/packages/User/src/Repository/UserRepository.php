<?php declare(strict_types=1);

namespace OpenRealEstate\User\Repository;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use OpenRealEstate\User\Entity\User;

final class UserRepository
{
    /**
     * @var ObjectRepository
     */
    private $objectRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->objectRepository = $entityManager->getRepository(User::class);
    }

    /**
     * @return User[]
     */
    public function fetchAll(): array
    {
        return $this->objectRepository->findAll();
    }
}
