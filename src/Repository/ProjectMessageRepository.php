<?php

namespace App\Repository;

use App\Entity\Project;
use App\Entity\ProjectMessage;
use App\Entity\User;
use App\Enum\MessageRole;
use App\Enum\UserRole;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProjectMessage>
 */
class ProjectMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjectMessage::class);
    }

    /**
     * Admin: unread client messages across all projects.
     * Client: unread designer messages across their own projects.
     */
    public function countUnreadForUser(User $user): int
    {
        $qb = $this->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->andWhere('m.read_at IS NULL');

        if ($user->getRole() === UserRole::ADMIN) {
            $qb->andWhere('m.role = :role')
                ->setParameter('role', MessageRole::CLIENT);
        } else {
            $qb->join('m.project', 'p')
                ->andWhere('p.user = :user')
                ->andWhere('m.role = :role')
                ->setParameter('user', $user)
                ->setParameter('role', MessageRole::DESIGNER);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Marks messages sent by the opposite role as read for the current viewer.
     */
    public function markReadForViewer(Project $project, UserRole $viewer): int
    {
        $targetRole = ($viewer === UserRole::ADMIN) ? MessageRole::CLIENT : MessageRole::DESIGNER;

        return $this->createQueryBuilder('m')
            ->update()
            ->set('m.read_at', ':now')
            ->andWhere('m.project = :project')
            ->andWhere('m.role = :targetRole')
            ->andWhere('m.read_at IS NULL')
            ->setParameter('now', new \DateTimeImmutable())
            ->setParameter('project', $project)
            ->setParameter('targetRole', $targetRole)
            ->getQuery()
            ->execute();
    }
}
