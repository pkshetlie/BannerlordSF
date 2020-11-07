<?php


namespace App\Service;


use App\Entity\Challenge;
use App\Entity\Run;
use Doctrine\ORM\EntityManagerInterface;

class RunService
{
    /** @var EntityManagerInterface */
    private EntityManagerInterface $em;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @param Run $run
     * @return void
     */
    public function ComputeScore(Run $run)
    {
        $score = 0;
        foreach ($run->getRunSettings() as $setting) {
            if ($setting->getChallengeSetting()->getIsUsedForScore()) {
                $score += $setting->getValue() * $setting->getChallengeSetting()->getRatio();
            }
        }
        $run->setScore($score);
        $run->setComputedScore($score * $run->getMalus());
    }
}