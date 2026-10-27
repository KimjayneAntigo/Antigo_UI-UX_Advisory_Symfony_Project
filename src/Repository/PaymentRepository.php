<?php

namespace App\Repository;

use App\Entity\Payment;
use App\Entity\Project;
use App\Entity\User;
use App\Enum\PaymentStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Payment>
 */
class PaymentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Payment::class);
    }

    public function countPending(): int
    {
        return (int) $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->andWhere('p.status = :status')
            ->setParameter('status', PaymentStatus::PENDING)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findLatestForProject(Project $project): ?Payment
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.project = :project')
            ->setParameter('project', $project)
            ->orderBy('p.submitted_at', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return list<Payment>
     */
    public function findRecentlyReviewedForUser(User $user, int $days = 7): array
    {
        $since = (new \DateTimeImmutable())->modify(sprintf('-%d days', $days));

        return $this->createQueryBuilder('p')
            ->andWhere('p.user = :user')
            ->andWhere('p.reviewed_at >= :since')
            ->setParameter('user', $user)
            ->setParameter('since', $since)
            ->orderBy('p.reviewed_at', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Finds payments by status, joined to project and user (instructor pattern).
     *
     * @return list<Payment>
     */
    public function findByStatus(PaymentStatus $s): array
    {
        return $this->createQueryBuilder('pm')
            ->leftJoin('pm.project', 'p')
            ->addSelect('p')
            ->leftJoin('pm.user', 'u')
            ->addSelect('u')
            ->andWhere('pm.status = :status')
            ->setParameter('status', $s)
            ->orderBy('pm.submitted_at', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

