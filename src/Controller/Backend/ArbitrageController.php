<?php

namespace App\Controller\Backend;

use App\Repository\ParticipationRepository;
use App\Service\ChallengeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/arbitrage")
 */
class ArbitrageController extends AbstractController
{
    /**
     * @Route("/", name="arbitrage_index", methods={"GET"})
     * @param ParticipationRepository $participationRepository
     * @param ChallengeService $challengeService
     * @return Response
     */
    public function index(ParticipationRepository $participationRepository, ChallengeService $challengeService): Response
    {
        $participations = $participationRepository->findByChallenge($challengeService->getRunningChallenge());
        return $this->render('backend/arbitrage/index.html.twig', [
            'participations' => $participations,

        ]);
    }
}
