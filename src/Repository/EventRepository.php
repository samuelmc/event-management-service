<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\City;

/**
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository implements CountableRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function countMethod(): callable
    {
        return [$this, 'countEvents'];
    }

    public function countEvents(?string $search): int
    {
        $count = $this->createQueryBuilder('event')
            ->select('COUNT(event.id)')
            ->where("event.title LIKE CONCAT('%', :search, '%')")
            ->setParameter('search', $search);

        return $count->getQuery()->getResult()[0][1];
    }

    public function countOwnEvents(User $user, ?string $search): int
    {
        $count = $this->createQueryBuilder('event')
            ->select('COUNT(event.id)')
            ->where("event.title LIKE CONCAT('%', :search, '%')")
            ->andWhere('event.createdBy = :user')
            ->setParameters(['user' => $user, 'search' => $search]);

        return $count->getQuery()->getResult()[0][1];
    }

    /**
     * @param int|null $page
     * @param int|null $itemsPerPage
     * @param string|null $search
     * @param string|null $sort
     * @param string|null $order
     * @param City|null $city
     * @return Event[]
     */
    public function findByPage(?int $page, ?int $itemsPerPage, string $search = '', string $sort = 'createdAt', string $order = 'desc', City $city = null)
    {
        if (!$page) $page = 1;
        if (!$itemsPerPage || !in_array($itemsPerPage, [10,20,50,100])) $itemsPerPage = 10;
        $offset = $itemsPerPage * ($page - 1);
        $order = strtoupper($order);

        $params = ['search' => $search, 'now' => (new \DateTime())];

        $queryBuilder = $this->createQueryBuilder('event')
            ->where("event.title LIKE CONCAT('%', :search, '%')")
            ->andWhere('event.endTime > :now')
            ->andWhere('event.active = 1');

        if ($city !== null){
            $queryBuilder->andWhere('event.city = :city');
            $params['city'] = $city;
        }

        $queryBuilder
            ->setParameters($params)
            ->orderBy("event.{$sort}", $order)
            ->setMaxResults($itemsPerPage)->setFirstResult($offset);

        return $queryBuilder->getQuery()->getResult();
    }

    public function findOwnEvents(User $user, ?int $page, ?int $itemsPerPage, string $search = '', string $sort = 'createdAt', string $order = 'desc') {

        if (!$page) $page = 1;
        if (!$itemsPerPage || !in_array($itemsPerPage, [10,20,50,100])) $itemsPerPage = 10;
        $offset = $itemsPerPage * ($page - 1);
        $order = strtoupper($order);

        $params = ['search' => $search, 'user' => $user];

        $queryBuilder = $this->createQueryBuilder('event')
            ->where("event.title LIKE CONCAT('%', :search, '%')")
            ->andWhere('event.createdBy = :user')
            ->setParameters($params)
            ->orderBy("event.{$sort}", $order)
            ->setMaxResults($itemsPerPage)->setFirstResult($offset);

        return $queryBuilder->getQuery()->getResult();
    }

    public function findHomePageEvents()
    {
        $queryBuilder = $this->createQueryBuilder('event')
            ->where('event.startTime > :now')
            ->setParameter('now', new \DateTime())
            ->orderBy('event.startTime', 'ASC')
            ->setMaxResults(5);

        return $queryBuilder->getQuery()->getResult();
    }
}
