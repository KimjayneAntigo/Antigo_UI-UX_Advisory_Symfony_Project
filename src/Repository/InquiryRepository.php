<?php

namespace App\Repository;

use App\Entity\Inquiry;
use App\Entity\User;
use App\Enum\InquiryStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Inquiry>
 */
class InquiryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Inquiry::class);
    }

    /**
     * @return list<Inquiry>
     */
    public function findNew(): array
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.status = :status')
            ->setParameter('status', InquiryStatus::NEW)
            ->orderBy('i.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function countNew(): int
    {
        return (int) $this->createQueryBuilder('i')
            ->select('COUNT(i.id)')
            ->andWhere('i.status = :status')
            ->setParameter('status', InquiryStatus::NEW)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return list<Inquiry>
     */
    public function findForClient(User $user): array
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.user = :user OR i.email = :email')
            ->setParameter('user', $user)
            ->setParameter('email', $user->getEmail())
            ->orderBy('i.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
