<?php


namespace App\Service;


use App\Entity\Challenge;
use App\Entity\Run;
use App\Entity\RunSettings;
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
    public function ComputeScore(Run $run, $computeOther = true)
    {
        $score = 0;
        $malusableScore = 0;

        $allruns = $this->em->getRepository(Run::class)
            ->createQueryBuilder('r')
            ->where('r.challenge = :challenge')
            ->setParameter('challenge', $run->getChallenge())
            ->getQuery()
            ->getResult();

        if ($run->getTempScore() != null) {
            $score = $run->getTempScore();
        } else {
            foreach ($run->getRunSettings() as $setting) {
                if ($setting->getChallengeSetting()->getIsUsedForScore()) {
                    if ($setting->getChallengeSetting()->getIsAffectedByMalus()) {
                        $malusableScore += $setting->getValue() * $setting->getChallengeSetting()->getRatio();
                    } else {
                        $score += floatval($setting->getValue()) * $setting->getChallengeSetting()->getRatio();
                    }
                    if ($setting->getChallengeSetting()->getIsReportedOnTheNextRun() && $computeOther) {
                        /** @var Run $r */
                        foreach ($allruns as $r) {
                            foreach ($r->getRunSettings() as $r_setting) {
                                if ($setting->getChallengeSetting() === $r_setting->getChallengeSetting() && $setting->getChallengeSetting()->getIsReportedOnTheNextRun()) {
                                    $r_setting->setValue($setting->getValue());
                                    $this->ComputeScore($r, false);
                                }
                            }
                        }
                    }
                }
            }
            $run->setScore($score);
        }
        $run->setComputedScore($score + ($malusableScore * $run->getMalus()));
    }
}