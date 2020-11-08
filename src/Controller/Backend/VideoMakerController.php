<?php

namespace App\Controller\Backend;


use App\Entity\Participation;
use App\Entity\User;
use App\Repository\ParticipationRepository;
use App\Repository\RunRepository;
use App\Service\ChallengeService;
use App\Service\RunService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\VarDumper\VarDumper;
use TwitchApi\TwitchApi;

/**
 * @Route("/admin/video-maker")
 */
class VideoMakerController extends AbstractController
{
    /**
     * @Route("/", name="video_maker_admin_new", methods={"GET","POST"})
     * @param Request $request
     * @param ChallengeService $challengeService
     * @param ParticipationRepository $participationRepository
     * @return Response
     * @throws \TwitchApi\Exceptions\ClientIdRequiredException
     * @throws \TwitchApi\Exceptions\InvalidLimitException
     * @throws \TwitchApi\Exceptions\InvalidTypeException
     * @throws \TwitchApi\Exceptions\TwitchApiException
     * @throws \TwitchApi\Exceptions\UnsupportedOptionException
     */
    public function current(Request $request, ChallengeService $challengeService, ParticipationRepository $participationRepository): Response
    {
        $options = [
            'client_id' => 'o7bf3gxsqjjaameb102lbkre19o32d',
        ];
        $challenge = $challengeService->getRunningChallenge();
        $participants = $participationRepository->findByChallenge($challenge);
        $clips = [];
        $twitchApi = new TwitchApi($options);

        $datesstart = $challenge->getChallengeDates()->first()->getStartDate();
        $datesend = $challenge->getChallengeDates()->last()->getEndDate();

        foreach ($participants as $participant) {
            if ($participant->getUser()->getTwitchID() != null) {
                $clips = array_merge($clips, $twitchApi->getTopClips(
                    $participant->getUser()->getTwitchID(),
                    null,
                    "Mount & Blade II: Bannerlord")['clips']
                );
            }
        }

        foreach ($clips as $i => $clip) {
            $date = new \DateTime($clip['created_at']);
            if ($date <= $datesstart || $date >= $datesend) {
                unset($clips[$i]);
            }
        }

        return $this->render('backend/video_maker/index.html.twig', [
            'clips' => $clips
        ]);

    }
}
