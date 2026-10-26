<?php

namespace App\Repository;

use App\Entity\Project;
use App\Entity\User;
use App\Enum\PaymentStatus;
use App\Enum\ProjectStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Project>
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    /**
     * @return list<Project>
     */
    public function findActiveForClient(User $user): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.user = :user')
            ->andWhere('p.status_type NOT IN (:closedStatuses)')
            ->setParameter('user', $user)
            ->setParameter('closedStatuses', [ProjectStatus::COMPLETED, ProjectStatus::CANCELLED])
            ->orderBy('p.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return list<Project>
     */
    public function findCancelled(): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.status_type = :cancelled OR p.cancelled_at IS NOT NULL')
            ->setParameter('cancelled', ProjectStatus::CANCELLED)
            ->orderBy('p.cancelled_at', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function countByStatus(ProjectStatus $status): int
    {
        return (int) $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->andWhere('p.status_type = :status')
            ->setParameter('status', $status)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * DQL SUM(budget) over projects that are neither completed nor cancelled.
     */
    public function getActivePipelineValue(): string
    {
        $sum = $this->createQueryBuilder('p')
            ->select('SUM(p.budget)')
            ->andWhere('p.status_type NOT IN (:closedStatuses)')
            ->setParameter('closedStatuses', [ProjectStatus::COMPLETED, ProjectStatus::CANCELLED])
            ->getQuery()
            ->getSingleScalarResult();

        return (string) ($sum ?? '0.00');
    }

    /**
     * Completed projects with an invoice sent, but without a verified payment.
     * Drives the client "invoice ready" banner.
     *
     * @return list<Project>
     */
    public function findAwaitingPaymentForClient(User $user): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.user = :user')
            ->andWhere('p.status_type = :completed')
            ->andWhere('p.invoice_sent_at IS NOT NULL')
            ->andWhere('NOT EXISTS (
                SELECT 1 FROM App\Entity\Payment pay
                WHERE pay.project = p AND pay.status = :verified
            )')
            ->setParameter('user', $user)
            ->setParameter('completed', ProjectStatus::COMPLETED)
            ->setParameter('verified', PaymentStatus::VERIFIED)
            ->orderBy('p.invoice_sent_at', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function generateNextProjectCode(): string
    {
        $maxId = (int) $this->createQueryBuilder('p')
            ->select('MAX(p.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $num = $maxId + 3001;
        while ($this->findOneBy(['project_code' => 'PRJ-' . $num]) !== null) {
            $num++;
        }

        return 'PRJ-' . $num;
    }
}
