<?php

namespace App\Controller\Backend;

use App\Entity\Challenge;
use App\Entity\ChallengeSetting;
use App\Entity\Participation;
use App\Entity\Run;
use App\Entity\RunSettings;
use App\Entity\User;
use App\Form\ChallengeType;
use App\Form\RunType;
use App\Repository\ChallengeRepository;
use App\Repository\RunRepository;
use App\Repository\UserRepository;
use App\Service\ChallengeService;
use Doctrine\Common\Collections\ArrayCollection;
use Pkshetlie\PaginationBundle\Service\Calcul;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/admin/run")
 */
class RunController extends AbstractController
{

    /**
     * @Route("/user/{id}", name="run_admin_current_new", methods={"GET","POST"})
     * @param Request $request
     * @param User $user
     * @param ChallengeService $challengeService
     * @return Response
     */
    public function current(Request $request, User $user, ChallengeService $challengeService, RunRepository $runRepository): Response
    {
        $challenge = $challengeService->getRunningChallenge();
        if ($challenge == null) {
            return new JsonResponse([
                'success' => false,
                "message" => "Aucun challenge en cours"
            ]);
        }
        $entityManager = $this->getDoctrine()->getManager();
        $run = $runRepository->createQueryBuilder('r')
            ->where('r.user = :user')
            ->andWhere('r.challenge  = :challenge')
            ->andWhere('r.endDate IS NULL')
            ->setParameter('challenge', $challenge)
            ->setParameter('user', $user)->getQuery()->getOneOrNullResult();

        if ($run == null) {
            $run = new Run();
            $run->setChallenge($challenge);
            $run->setUser($user);
            $entityManager->persist($run);
            $run->setStartDate(new \DateTime());
            foreach ($challenge->getChallengeSettings() as $setting) {
                $runSetting = new RunSettings();
                $runSetting->setChallengeSetting($setting);
                $runSetting->setRun($run);
                $run->addRunSetting($runSetting);
                $entityManager->persist($runSetting);
            }
            $entityManager->flush();
        }

        $form = $this->createForm(RunType::class, $run, [
            'attr' => ['id' => 'runForm'],
            'action' => $this->generateUrl('run_admin_current_new', ['id' => $user->getId()])
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
        }
        return new JsonResponse([
            'success' => true,
            'html' => $this->renderView('backend/run/_form.html.twig', [
                'form' => $form->createView(),
                'challenger' => $user,
            ])
        ]);

    }

    /**
     * @Route("/delete-participation/{id}", name="challenge_admin_delete_participation", methods={"GET","POST"})
     * @param Request $request
     * @param Participation $participation
     * @return Response
     */
    public function deleteParticipation(Request $request, Participation $participation, \Swift_Mailer $mailer): Response
    {
        $this->getDoctrine()->getManager()->remove($participation);

        return new JsonResponse([
            'success' => true,
            'replace' => ""
        ]);
    }

    /**
     * @Route("/toggle-participation/{id}", name="challenge_admin_toggle_participation", methods={"GET","POST"})
     * @param Request $request
     * @param Participation $participation
     * @return Response
     */
    public function toggleParticipation(Request $request, Participation $participation, \Swift_Mailer $mailer): Response
    {
        $participation->setEnabled(!$participation->getEnabled());
        $this->getDoctrine()->getManager()->flush();
        $message = (new \Swift_Message('Validation de votre inscription au challenge ' . $participation->getChallenge()->getTitle()))
            ->setFrom($this->getParameter('webmaster_email'))
            ->setTo($participation->getUser()->getEmail())
            ->setBody(
                $this->renderView(
                // templates/emails/registration.html.twig
                    "mails/challenge/validated.html.twig",
                    ['challenge' => $participation->getChallenge()]
                ),
                'text/html'
            )

            // you can remove the following code if you don't define a text version for your emails
            ->addPart(
                $this->renderView(
                // templates/emails/registration.txt.twig
                    "mails/challenge/validated.html.twig",
                    ['challenge' => $participation->getChallenge()]
                ),
                'text/plain'
            );
        try {

            $mailer->send($message);
        } catch (\Exception $e) {
            $x = $e;
        }
        return new JsonResponse([
            'success' => true,
            'replace' => $participation->getEnabled() ? "<i class='fas fa-check text-success'></i>" : "<i class='fas fa-times text-danger'></i>"
        ]);
    }

    /**
     * @Route("/{id}/edit", name="challenge_admin_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Challenge $challenge
     * @param SluggerInterface $slugger
     * @param UserRepository $userRepository
     * @return Response
     */
    public function edit(Request $request, Challenge $challenge, SluggerInterface $slugger, UserRepository $userRepository): Response
    {
        $originalDates = new ArrayCollection();
        foreach ($challenge->getChallengeDates() as $date) {
            $originalDates->add($date);
        }
        $originalPrizes = new ArrayCollection();
        foreach ($challenge->getChallengePrizes() as $prize) {
            $originalPrizes->add($prize);
        }


        $form = $this->createForm(ChallengeType::class, $challenge);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $banner = $form->get('banner')->getData();
            foreach ($originalDates as $date) {
                if (false === $challenge->getChallengeDates()->contains($date)) {
                    $date->setChallenge(null);
                    $entityManager->persist($date);
                    $entityManager->remove($date);
                }
            }
            foreach ($originalPrizes as $prize) {
                if (false === $challenge->getChallengePrizes()->contains($prize)) {
                    $prize->setChallenge(null);
                    $entityManager->persist($prize);
                    $entityManager->remove($prize);
                }
            }

            foreach ($challenge->getChallengeDates() as $challengeDate) {
                $challengeDate->setChallenge($challenge);
            }

            foreach ($challenge->getChallengePrizes() as $challengePrize) {
                $challengePrize->setChallenge($challenge);
            }
            if ($banner) {
                $originalFilename = pathinfo($banner->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $banner->guessExtension();
                try {
                    $banner->move(
                        $this->getParameter('challenge_banner_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }
                $challenge->setBanner($newFilename);
            }

            $entityManager->persist($challenge);
            $entityManager->flush();

            return $this->redirectToRoute('challenge_admin_index');
        }
        $allPlayers = $userRepository->createQueryBuilder('u')
            ->orderBy('u.username')
            ->getQuery()
            ->getResult();

        $availablePlayer = [];
        foreach ($allPlayers as $user) {
            $result = true;
            foreach ($user->getParticipations() as $participation) {
                if ($participation->getChallenge() === $challenge) {
                    $result = false;
                }
            }
            if ($result) {
                $availablePlayer[] = $user;
            }
        }

        $arbitres = $userRepository->createQueryBuilder('u')
            ->where('u.roles LIKE :employee')
            ->setParameter('employee', '%ROLE_ARBITRE%')
            ->orderBy("u.username")
            ->getQuery()->getResult();
        return $this->render('backend/challenge/create_edit.html.twig', [
            'challenge' => $challenge,
            'form' => $form->createView(),
            'arbitres' => $arbitres,
            'availablePlayer' => $availablePlayer,
        ]);
    }

    /**
     * @Route("/arbitre/change/{id}", name="change_arbitre")
     * @param Request $request
     * @param Participation $participation
     */
    public function changeArbitre(Request $request, Participation $participation, UserRepository $userRepository)
    {
        $arbitre = $userRepository->find($request->get('arbitre'));
        if ($arbitre != null) {
            $participation->setArbitre($arbitre);
            $this->getDoctrine()->getManager()->flush();
            return new JsonResponse([
                'success' => true,
                "message" => ""
            ]);
        } else {
            $participation->setArbitre(null);
            $this->getDoctrine()->getManager()->flush();

        }
        return new JsonResponse([
            'success' => false,
            "message" => "Arbitre non trouvé."
        ]);
    }

    /**
     * @Route("/{id}", name="challenge_admin_delete", methods={"GET"})
     * @param Request $request
     * @param Challenge $challenge
     * @return Response
     */
    public function delete(Request $request, Challenge $challenge): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($challenge);
        $entityManager->flush();

        return $this->redirectToRoute('challenge_admin_index');
    }

    /**
     * @Route("/add-participation/{id}", name="add_participation")
     * @param Request $request
     * @param Challenge $challenge
     * @param UserRepository $userRepository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addParticipation(Request $request, Challenge $challenge, UserRepository $userRepository)
    {
        foreach ($request->get('participations') as $userId) {
            $user = $userRepository->find($userId);
            $participation = new Participation();
            $participation
                ->setChallenge($challenge)
                ->setEnabled(true)
                ->setUser($user);
            $entityManger = $this->getDoctrine()->getManager();
            $entityManger->persist($participation);
            $entityManger->flush();
        }
        return $this->redirectToRoute("challenge_admin_edit", ["id" => $challenge->getId()]);
    }
}