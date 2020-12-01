<?php

namespace App\Service;

use App\Entity\Challenge;
use App\Entity\Participation;
use App\Entity\User;
use App\Repository\ChallengeRepository;
use Doctrine\ORM\EntityManagerInterface;

class StatistiquesService
{
    /** @var EntityManagerInterface */
    private EntityManagerInterface $em;
    /**
     * @var ChallengeRepository
     */
    private ChallengeRepository $challengeRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public function countChallenges()
    {
        return $this->em->getRepository(Challenge::class)->count([]);
    }

    public function countParticipations()
    {
        return $this->em->getRepository(Participation::class)->count(["enabled"=>true]);
    }

    public function countTwitchers()
    {
        return count($this->em->getRepository(User::class)->createQueryBuilder('u')->where("u.twitchID is not null")
            ->getQuery()->getResult());

    }
}