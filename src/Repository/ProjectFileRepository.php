<?php

namespace App\Repository;

use App\Entity\Project;
use App\Entity\ProjectFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProjectFile>
 */
class ProjectFileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjectFile::class);
    }

    /**
     * @return list<ProjectFile>
     */
    public function findVisibleForClient(Project $project): array
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.project = :project')
            ->andWhere('f.deleted_by_client_at IS NULL')
            ->setParameter('project', $project)
            ->orderBy('f.uploaded_at', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return list<ProjectFile>
     */
    public function findAllForAdmin(Project $project): array
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.project = :project')
            ->setParameter('project', $project)
            ->orderBy('f.uploaded_at', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
