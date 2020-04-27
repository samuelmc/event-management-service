<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    /**
     * @param int|null $page
     * @param int|null $itemsPerPage
     * @param string|null $search
     * @param string|null $sort
     * @param string|null $order
     * @return Event[]
     */
    public function findByPage(?int $page, ?int $itemsPerPage, string $search = '', string $sort = 'createdAt', string $order = 'desc')
    {
        if (!$page) $page = 1;
        if (!$itemsPerPage || !in_array($itemsPerPage, [10,20,50,100])) $itemsPerPage = 10;
        $offset = $itemsPerPage * ($page - 1);
        $order = strtoupper($order);

        $queryBuilder = $this->createQueryBuilder('event')
            ->where("event.title LIKE CONCAT('%', :search, '%')")
            ->setParameter('search', $search)
            ->orderBy("event.{$sort}", $order)
            ->setMaxResults($itemsPerPage)->setFirstResult($offset);

        return $queryBuilder->getQuery()->getResult();
    }

    // /**
    //  * @return Event[] Returns an array of Event objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Event
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
