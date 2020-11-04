<?php

namespace App\Controller\Backend;

use App\Repository\ParticipationRepository;
use App\Service\ChallengeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/leaderboard")
 */
class LeaderboardController extends AbstractController
{
    /**
     * @Route("/", name="admin_leaderboard_index", methods={"GET"})
     * @param ParticipationRepository $participationRepository
     * @param ChallengeService $challengeService
     * @return Response
     */
    public function index(ParticipationRepository $participationRepository, ChallengeService $challengeService): Response
    {
        $participations = $participationRepository->findByChallengeAndOrderByScore($challengeService->getRunningChallenge());
        $scores = [];
        foreach($participations AS $participation){

        }


        return $this->render('backend/leaderboard/index.html.twig', [
            'scores' => $scores,
        ]);
    }
}
