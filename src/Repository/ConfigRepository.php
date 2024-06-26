<?php

namespace App\Repository;

use App\Entity\Config;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Config|null find($id, $lockMode = null, $lockVersion = null)
 * @method Config|null findOneBy(array $criteria, array $orderBy = null)
 * @method Config[]    findAll()
 * @method Config[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConfigRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Config::class);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function deleteAll(): int
    {
        $qb = $this->createQueryBuilder('c');

        $qb->delete();

        return $qb->getQuery()->getSingleScalarResult() ?? 0;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findOneByAllowInstall($value): ?Config
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.name = :allow_install')
            ->setParameter('allow_install', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findAlertDateRef()
    {
        return $this->createQueryBuilder('c')
            ->select('c.value')
            ->where('c.name = :name')
            ->setParameter('name', 'alert_date_ref')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function setAlertDateRef($newDate)
{
    $entityManager = $this->getEntityManager();

    // Supposons que votre entité de configuration s'appelle Config
    $config = $this->findOneBy(['name' => 'alert_date_ref']);

    if ($config) {
        $config->setValue($newDate);
        $entityManager->persist($config);
        $entityManager->flush();
    }
}


    // /**
    //  * @return Config[] Returns an array of Config objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Config
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
