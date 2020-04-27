<?php

namespace App\Repository;

use App\Entity\City;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method City|null find($id, $lockMode = null, $lockVersion = null)
 * @method City|null findOneBy(array $criteria, array $orderBy = null)
 * @method City[]    findAll()
 * @method City[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, City::class);
    }

    /**
     * @param int|null $page
     * @param int|null $itemsPerPage
     * @param string|null $search
     * @param string|null $sort
     * @param string|null $order
     *
     * @return City[]
     */
    public function findByPage(?int $page, ?int $itemsPerPage, string $search = '', string $sort = 'createdAt', string $order = 'desc')
    {
        if (!$page) $page = 1;
        if (!$itemsPerPage || !in_array($itemsPerPage, [10,20,50,100])) $itemsPerPage = 10;
        if (!$search) $search = '';
        $offset = $itemsPerPage * ($page - 1);
        $order = strtoupper($order);

        $orderByString = $sort == 'events' ? "city.createdAt" : "city.{$sort}";

        /** @var City[] $cities */
        $cities = $this->createQueryBuilder('city')
            ->where("city.name LIKE CONCAT('%', :search, '%')")
            ->setParameter('search', $search)
            ->orderBy($orderByString, $order)
            ->setMaxResults($itemsPerPage)->setFirstResult($offset)
            ->getQuery()->getResult();

        if ($sort == 'events') {
            usort($cities, [$this, "sortByEventsCount{$order}"]);
        }

        return $cities;
    }

    private function sortByEventsCountASC(City $a, City $b) {
        if (count($a->getEvents()) > count($b->getEvents())) return 1;
        if (count($a->getEvents()) < count($b->getEvents())) return -1;
        return 0;
    }

    private function sortByEventsCountDESC(City $a, City $b) {
        if (count($a->getEvents()) > count($b->getEvents())) return -1;
        if (count($a->getEvents()) < count($b->getEvents())) return 1;
        return 0;
    }
}
