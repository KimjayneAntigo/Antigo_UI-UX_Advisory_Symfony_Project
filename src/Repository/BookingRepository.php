<?php

namespace App\Repository;

use App\Entity\Booking;
use App\Entity\User;
use App\Enum\BookingStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Booking>
 */
class BookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    /**
     * @return list<Booking>
     */
    public function findUpcoming(int $limit = 5): array
    {
        $today = new \DateTimeImmutable('today');

        return $this->createQueryBuilder('b')
            ->andWhere('b.booking_date >= :today')
            ->andWhere('b.status != :cancelled')
            ->setParameter('today', $today)
            ->setParameter('cancelled', BookingStatus::CANCELLED)
            ->orderBy('b.booking_date', 'ASC')
            ->addOrderBy('b.booking_time', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return list<Booking>
     */
    public function findForClient(User $user): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.user = :user OR b.guest_email = :email')
            ->setParameter('user', $user)
            ->setParameter('email', $user->getEmail())
            ->orderBy('b.booking_date', 'DESC')
            ->addOrderBy('b.booking_time', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Finds bookings scheduled within an inclusive date range.
     *
     * @return list<Booking>
     */
    public function findBetween(\DateTimeImmutable $from, \DateTimeImmutable $to): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.booking_date >= :from')
            ->andWhere('b.booking_date <= :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->orderBy('b.booking_date', 'ASC')
            ->addOrderBy('b.booking_time', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

