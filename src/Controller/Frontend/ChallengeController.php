<?php

namespace App\Controller\Frontend;

use App\Entity\Challenge;
use App\Entity\Participation;
use App\Entity\User;
use App\Form\ChallengeType;
use App\Repository\ChallengeRepository;
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
            ->orderBy('c.registrationOpening', 'DESC');
        $paginator = $paginationService->process($qb, $request);
        return $this->render('frontend/challenge/index.html.twig', [
            'paginator' => $paginator,
        ]);
    }

    /**
     * @Route("/participer/{id}", name="challenge_participer")
     * @param Request $request
     * @param Challenge $challenge
     * @return Response
     */
    public function registerToChallenge(Request $request, Challenge $challenge)
    {
        $form = $this->createFormBuilder()
            ->add('confirmation', CheckboxType::class, ['required' => true, 'label' => "Je souhaite m'inscrire à cette édition"])
            ->add('inscription', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->isGranted('ROLE_USER')) {
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
                $this->addFlash('danger', "Vous devez êtres connecté pour pouvoir vous inscrire.");
            }
        }
        return $this->render('frontend/challenge/register.html.twig', [
            'challenge' => $challenge,
            'form' => $form->createView()
        ]);
    }
}
