<?php

namespace App\Repository;

use App\Entity\ScheduleItem;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ScheduleItem>
 */
class ScheduleItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ScheduleItem::class);
    }

    /**
     * @param int $scheduleId
     * @param int $dayOfWeek
     * @param DateTime $startTime
     * @param DateTime $endTime
     * @param int|null $excludeId
     * @return ScheduleItem[]
     */
    public function findOverlappingItems(int $scheduleId, int $dayOfWeek, DateTime $startTime, DateTime $endTime, ?int $excludeId = null): array
    {
        $qb = $this->createQueryBuilder('s')
            ->where('s.schedule = :scheduleId')
            ->andWhere('s.dayOfWeek = :dayOfWeek')
            ->andWhere('(:startTime BETWEEN s.startTime AND s.endTime OR :endTime BETWEEN s.startTime AND s.endTime OR (s.startTime = :startTime OR s.endTime = :endTime))')
            ->setParameter('scheduleId', $scheduleId)
            ->setParameter('dayOfWeek', $dayOfWeek)
            ->setParameter('startTime', $startTime)
            ->setParameter('endTime', $endTime);

        if ($excludeId) {
            $qb->andWhere('s.id <> :excludeId')
                ->setParameter('excludeId', $excludeId);
        }

        return $qb->getQuery()->getResult();
    }
}
