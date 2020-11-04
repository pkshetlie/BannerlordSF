<?php

namespace App\Repository;

use App\Entity\Challenge;
use App\Entity\Run;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Run|null find($id, $lockMode = null, $lockVersion = null)
 * @method Run|null findOneBy(array $criteria, array $orderBy = null)
 * @method Run[]    findAll()
 * @method Run[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RunRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Run::class);
    }

    public function findByScore(Challenge $challenge)
    {
        $qb = $this->createQueryBuilder('r')
            ->addSelect('SUM(s.value*sc.ratio)*r.malus AS score')
            ->leftJoin('r.runSettings', 's')
            ->leftJoin('r.challenge', 'c')
            ->leftJoin('s.challengeSetting','sc')
            ->where('r.challenge = :challenge')
            ->setParameter('challenge', $challenge);


        return $qb->getQuery()->getResult();
    }
}
