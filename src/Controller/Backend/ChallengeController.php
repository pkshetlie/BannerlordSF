<?php

namespace App\Controller\Backend;

use App\Entity\Challenge;
use App\Entity\Participation;
use App\Form\ChallengeType;
use App\Repository\ChallengeRepository;
use App\Repository\UserRepository;
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
 * @Route("/admin/challenge")
 */
class ChallengeController extends AbstractController
{
    /**
     * @Route("/", name="challenge_admin_index", methods={"GET"})
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
        return $this->render('backend/challenge/index.html.twig', [
            'paginator' => $paginator,
        ]);
    }

    /**
     * @Route("/new", name="challenge_admin_new", methods={"GET","POST"})
     * @param Request $request
     * @param SluggerInterface $slugger
     * @param UserRepository $userRepository
     * @return Response
     */
    public function new(Request $request, SluggerInterface $slugger,UserRepository $userRepository): Response
    {
        return $this->edit($request, new Challenge(), $slugger,$userRepository);
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
     * @return Response
     */
    public function edit(Request $request, Challenge $challenge, SluggerInterface $slugger, UserRepository $userRepository): Response
    {

        $originalDates = new ArrayCollection();

        // Create an ArrayCollection of the current Tag objects in the database
        foreach ($challenge->getChallengeDates() as $date) {
            $originalDates->add($date);
        }
        $form = $this->createForm(ChallengeType::class, $challenge);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $banner = $form->get('banner')->getData();
            foreach ($originalDates as $date) {
                if (false === $challenge->getChallengeDates()->contains($date)) {
                    // remove the Task from the Tag
                    $date->getChallenge()->removeElement($challenge);

                    // if it was a many-to-one relationship, remove the relationship like this
                    $date->setChallenge(null);

                    $entityManager->persist($date);

                    // if you wanted to delete the Tag entirely, you can also do that
//                    $entityManager->remove($date);
                }
            }
            foreach($challenge->getChallengeDates() AS $challengeDate){
                $challengeDate->setChallenge($challenge);
            }
            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($banner) {
                $originalFilename = pathinfo($banner->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $banner->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $banner->move(
                        $this->getParameter('challenge_banner_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $challenge->setBanner($newFilename);
            }

            $entityManager->persist($challenge);
            $entityManager->flush();

            return $this->redirectToRoute('challenge_admin_index');
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
        }else{
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
}
