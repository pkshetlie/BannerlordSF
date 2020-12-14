<?php

namespace App\Controller\Frontend;

use App\Entity\Challenge;
use App\Entity\ChallengeNewsletter;
use App\Entity\Participation;
use App\Entity\User;
use App\Form\ChallengeType;
use App\Repository\ChallengeNewsletterRepository;
use App\Repository\ChallengeRepository;
use App\Repository\ParticipationRepository;
use Pkshetlie\PaginationBundle\Service\Calcul;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/challenge")
 */
class ChallengeController extends AbstractController
{
    /**
     * @Route("/", name="challenge_index", methods={"GET"})
     * @param Request $request
     * @param ChallengeRepository $challengeRepository
     * @param Calcul $paginationService
     * @return Response
     */
    public function index(Request $request, ChallengeRepository $challengeRepository, Calcul $paginationService): Response
    {
        $qb = $challengeRepository->createQueryBuilder('c')
            ->where('c.display = true')
            ->andWhere('c.user IS NULL')
            ->orderBy('c.season', 'DESC')
            ->addOrderBy('c.registrationOpening', 'DESC');
        $paginator = $paginationService->setDefaults(9)->process($qb, $request);
        if($paginator->isPartial()){
            return $this->render('frontend/challenge/partial/challenges.html.twig', [
                'paginator' => $paginator,
            ]);
        }
        return $this->render('frontend/challenge/index.html.twig', [
            'paginator' => $paginator,
        ]);
    }

    /**
     * @Route("/newsletter-register", name="newsletter_register_challenge", methods={"GET"})
     * @param Request $request
     * @param ChallengeRepository $challengeRepository
     * @param Calcul $paginationService
     * @return Response
     */
    public function newsletterRegister(Request $request, ChallengeRepository $challengeRepository, ChallengeNewsletterRepository $challengeNewsletterRepository): Response
    {
        $challenge = $challengeRepository->find($request->get('newsletter_challenge'));
        if ($challenge != null) {
            $newsletter = $challengeNewsletterRepository->findBy([
                'email' => $request->get('newsletter_email'),
                'challenge' => $challenge
            ]);
            if ($newsletter == null) {
                $newsletter = new ChallengeNewsletter();
                $newsletter->setEmail($request->get('newsletter_email'));
                $newsletter->setChallenge($challenge);
                $em = $this->getDoctrine()->getManager();
                $em->persist($newsletter);
                $em->flush();

                $this->addFlash('success', "Nous avons bien enregistré votre email afin de vous prévenir de l'ouverture des inscriptions.");
            }
            $this->addFlash('danger', "Vous êtes déjà inscrit pour ce challenge.");
        } else {
            $this->addFlash('danger', "Probleme lors de la recherche du challenge qui vous interesse.");
        }

        return $this->redirectToRoute('challenge_index');

    }

    /**
     * @Route("/participer/{id}", name="challenge_participer")
     * @param Request $request
     * @param Challenge $challenge
     * @param ParticipationRepository $participationRepository
     * @return Response
     */
    public function registerToChallenge(Request $request, Challenge $challenge, ParticipationRepository $participationRepository)
    {
        $form = $this->createFormBuilder()
            ->add('inscription', SubmitType::class, [
                'attr' => ['class' => "btn btn-custom btn-green active"]

            ])
            ->getForm();
        $participants = $challenge->getParticipations()->filter(function (Participation $p) {
            return $p->getUser()->getUsername();
        });
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->isGranted('ROLE_USER')) {
                if ($participationRepository->findBy([
                        'user' => $this->getUser(),
                        'challenge' => $challenge
                    ]) == null) {
                    /** @var User $user */
                    $user = $this->getUser();
                    $participation = new Participation();
                    $participation->setChallenge($challenge);
                    $participation->setUser($user);
                    $participation->setEnabled(false);
                    $user->addParticipation($participation);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($participation);
                    $em->flush();
                    $this->addFlash('success', "Votre demande est soumise à validation d'un membre du staff, vous recevrez un mail dès que celle ci sera validée.");
                } else {
                    $this->addFlash('danger', "Vous êtres déjà inscrit à ce tournois.");

                }
            } else {
                $this->addFlash('danger', "Vous devez êtres connecté pour pouvoir vous inscrire.");
            }
        }

        $participations = $challenge->getLeaderBoard();

        return $this->render('frontend/challenge/register.html.twig', [
            'challenge' => $challenge,
            'participations' => $participants,
            'leaderboard' => $participations,
            'form' => $form->createView()
        ]);
    }
}
