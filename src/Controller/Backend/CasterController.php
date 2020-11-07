<?php

namespace App\Controller\Backend;

use App\Entity\Participation;
use App\Repository\ParticipationRepository;
use App\Service\ChallengeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/caster")
 */
class CasterController extends AbstractController
{
    /**
     * @Route("/", name="caster_index", methods={"GET"})
     * @param ParticipationRepository $participationRepository
     * @param ChallengeService $challengeService
     * @return Response
     */
    public function index(ParticipationRepository $participationRepository, ChallengeService $challengeService): Response
    {
        $challenge = $challengeService->getRunningChallenge();
        /** @var Participation[] $participations */
        $participations = $participationRepository->findByChallenge($challenge);


        return $this->render('backend/caster/index.html.twig', [
            'participations' => $participations,
            'challenge' => $challenge,

        ]);
    }
}
